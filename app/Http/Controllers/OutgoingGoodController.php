<?php

namespace App\Http\Controllers;

use App\Models\OutgoingGood;
use App\Models\Item;
use App\Models\Menu;
use Illuminate\Http\Request;

class OutgoingGoodController extends Controller
{
    public function index(Request $request)
    {
        $query = OutgoingGood::with(['item', 'menu']);
        
        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        // Menu filter
        if ($request->filled('menu_id')) {
            $query->where('menu_id', $request->menu_id);
        }
        
        $outgoingGoods = $query->orderBy('date', 'desc')->paginate(15);
        $menus = Menu::orderBy('name')->get();
        
        return view('outgoing-goods.index', compact('outgoingGoods', 'menus'));
    }

    public function create()
    {
        $items = Item::where('stock_quantity', '>', 0)->orderBy('name')->get();
        $menus = Menu::with('ingredients')->orderBy('name')->get();
        return view('outgoing-goods.create', compact('items', 'menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'menu_id' => 'nullable|exists:menus,id',
            'quantity' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        // Check stock availability
        $item = Item::find($validated['item_id']);
        if ($item->stock_quantity < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi. Stok tersedia: ' . $item->stock_quantity . ' ' . $item->unit])
                ->withInput();
        }
        
        // Create outgoing good (stock will be reduced automatically via model boot)
        OutgoingGood::create($validated);
        
        return redirect()->route('outgoing-goods.index')
            ->with('success', 'Barang keluar berhasil dicatat. Stok telah dikurangi.');
    }
    
    /**
     * Quick create from menu - creates multiple outgoing goods for all ingredients
     */
    public function storeFromMenu(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'multiplier' => 'required|numeric|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        $menu = Menu::with('ingredients')->find($validated['menu_id']);
        
        // Check stock for all ingredients
        foreach ($menu->ingredients as $ingredient) {
            $requiredQty = $ingredient->pivot->quantity_required * $validated['multiplier'];
            if ($ingredient->stock_quantity < $requiredQty) {
                return back()->withErrors([
                    'menu_id' => "Stok {$ingredient->name} tidak mencukupi. Dibutuhkan: {$requiredQty} {$ingredient->unit}, Tersedia: {$ingredient->stock_quantity} {$ingredient->unit}"
                ])->withInput();
            }
        }
        
        // Create outgoing goods for all ingredients
        foreach ($menu->ingredients as $ingredient) {
            OutgoingGood::create([
                'item_id' => $ingredient->id,
                'menu_id' => $menu->id,
                'quantity' => $ingredient->pivot->quantity_required * $validated['multiplier'],
                'date' => $validated['date'],
                'notes' => $validated['notes'] ?? "Pemakaian untuk menu: {$menu->name}",
            ]);
        }
        
        return redirect()->route('outgoing-goods.index')
            ->with('success', 'Pemakaian bahan untuk menu "' . $menu->name . '" berhasil dicatat.');
    }

    public function show(OutgoingGood $outgoingGood)
    {
        $outgoingGood->load(['item', 'menu']);
        return view('outgoing-goods.show', compact('outgoingGood'));
    }

    public function edit(OutgoingGood $outgoingGood)
    {
        $items = Item::orderBy('name')->get();
        $menus = Menu::orderBy('name')->get();
        return view('outgoing-goods.edit', compact('outgoingGood', 'items', 'menus'));
    }

    public function update(Request $request, OutgoingGood $outgoingGood)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'menu_id' => 'nullable|exists:menus,id',
            'quantity' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        // 1. Restore old stock (Global + Batches LIFO)
        OutgoingGood::restoreStock($outgoingGood);
        
        // 2. Check new stock availability
        $newItem = Item::find($validated['item_id']);
        
        // If we are updating the same item, the stock is already restored, so we check against current stock (which includes the restored amount)
        if ($newItem->stock_quantity < $validated['quantity']) {
            // Revert the restoration (Global + Batches FIFO deduction) to go back to original state
            OutgoingGood::deductStock($outgoingGood);
            
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi. Stok tersedia: ' . $newItem->stock_quantity . ' ' . $newItem->unit])
                ->withInput();
        }
        
        // 3. Update the record
        $outgoingGood->update($validated);
        
        // 4. Deduct new stock (Global + Batches FIFO)
        // We need to reload the relationship or just pass the object, but if item_id changed, 
        // deductStock needs to access the NEW item.
        // Since update() flushes relationships, accessing $outgoingGood->item will query the new item.
        // However, to be safe, we can unset the relation.
        $outgoingGood->unsetRelation('item'); 
        OutgoingGood::deductStock($outgoingGood);
        
        return redirect()->route('outgoing-goods.index')
            ->with('success', 'Barang keluar berhasil diperbarui.');
    }

    public function destroy(OutgoingGood $outgoingGood)
    {
        // Stock will be restored automatically via model boot
        $outgoingGood->delete();
        
        return redirect()->route('outgoing-goods.index')
            ->with('success', 'Barang keluar berhasil dihapus. Stok telah dikembalikan.');
    }
}
