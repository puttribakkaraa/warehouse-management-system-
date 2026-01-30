<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Item;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::withCount('ingredients');
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $menus = $query->orderBy('name')->paginate(15);
        
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        return view('menus.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|array',
            'ingredients.*.item_id' => 'required|exists:items,id',
            'ingredients.*.quantity_required' => 'required|numeric|min:0.01',
        ]);
        
        $menu = Menu::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);
        
        // Attach ingredients
        if (!empty($validated['ingredients'])) {
            foreach ($validated['ingredients'] as $ingredient) {
                $menu->ingredients()->attach($ingredient['item_id'], [
                    'quantity_required' => $ingredient['quantity_required'],
                ]);
            }
        }
        
        return redirect()->route('menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function show(Menu $menu)
    {
        $menu->load('ingredients');
        return view('menus.show', compact('menu'));
    }

    public function edit(Menu $menu)
    {
        $menu->load('ingredients');
        $items = Item::orderBy('name')->get();
        return view('menus.edit', compact('menu', 'items'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|array',
            'ingredients.*.item_id' => 'required|exists:items,id',
            'ingredients.*.quantity_required' => 'required|numeric|min:0.01',
        ]);
        
        $menu->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);
        
        // Sync ingredients
        $menu->ingredients()->detach();
        if (!empty($validated['ingredients'])) {
            foreach ($validated['ingredients'] as $ingredient) {
                $menu->ingredients()->attach($ingredient['item_id'], [
                    'quantity_required' => $ingredient['quantity_required'],
                ]);
            }
        }
        
        return redirect()->route('menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        
        return redirect()->route('menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }
}
