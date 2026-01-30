<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();
        
        // Search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'aman':
                    $query->whereColumn('stock_quantity', '>', 'minimum_stock');
                    break;
                case 'hampir_habis':
                    $query->whereColumn('stock_quantity', '<=', 'minimum_stock')
                          ->where('stock_quantity', '>', 0);
                    break;
                case 'habis':
                    $query->where('stock_quantity', '<=', 0);
                    break;
            }
        }
        
        $items = $query->orderBy('name')->paginate(15);
        
        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock_quantity' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['minimum_stock'] = $validated['minimum_stock'] ?? 0;
        
        Item::create($validated);
        
        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load(['incomingGoods.supplier', 'outgoingGoods.menu']);
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'minimum_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        $validated['minimum_stock'] = $validated['minimum_stock'] ?? 0;
        
        $item->update($validated);
        
        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        
        return redirect()->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
