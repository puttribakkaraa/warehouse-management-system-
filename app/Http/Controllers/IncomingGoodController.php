<?php

namespace App\Http\Controllers;

use App\Models\IncomingGood;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;

class IncomingGoodController extends Controller
{
    public function index(Request $request)
    {
        $query = IncomingGood::with(['item', 'supplier']);
        
        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }
        
        // Supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        $incomingGoods = $query->orderBy('date', 'desc')->paginate(15);
        $suppliers = Supplier::orderBy('name')->get();
        
        return view('incoming-goods.index', compact('incomingGoods', 'suppliers'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('incoming-goods.create', compact('items', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];
        
        // Create incoming good (stock will be updated automatically via model boot)
        IncomingGood::create($validated);
        
        return redirect()->route('incoming-goods.index')
            ->with('success', 'Barang masuk berhasil dicatat. Stok telah diperbarui.');
    }

    public function show(IncomingGood $incomingGood)
    {
        $incomingGood->load(['item', 'supplier']);
        return view('incoming-goods.show', compact('incomingGood'));
    }

    public function edit(IncomingGood $incomingGood)
    {
        $items = Item::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('incoming-goods.edit', compact('incomingGood', 'items', 'suppliers'));
    }

    public function update(Request $request, IncomingGood $incomingGood)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        // Revert old stock
        $oldItem = $incomingGood->item;
        $oldItem->stock_quantity -= $incomingGood->quantity;
        $oldItem->save();
        
        $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];
        $incomingGood->update($validated);
        
        // Add new stock
        $newItem = Item::find($validated['item_id']);
        $newItem->stock_quantity += $validated['quantity'];
        $newItem->save();
        
        return redirect()->route('incoming-goods.index')
            ->with('success', 'Barang masuk berhasil diperbarui.');
    }

    public function destroy(IncomingGood $incomingGood)
    {
        // Stock will be reverted automatically via model boot
        $incomingGood->delete();
        
        return redirect()->route('incoming-goods.index')
            ->with('success', 'Barang masuk berhasil dihapus. Stok telah disesuaikan.');
    }
}
