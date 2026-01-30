<?php

namespace App\Http\Controllers;

use App\Models\SupplierBill;
use App\Models\Supplier;
use App\Models\IncomingGood;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SupplierBillController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierBill::with('supplier');
        
        // Supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Year filter
        if ($request->filled('year')) {
            $query->where('period_year', $request->year);
        }
        
        // Month filter
        if ($request->filled('month')) {
            $query->where('period_month', $request->month);
        }
        
        $bills = $query->orderBy('period_year', 'desc')
            ->orderBy('period_month', 'desc')
            ->paginate(15);
            
        $suppliers = Supplier::orderBy('name')->get();
        
        // Get available years
        $years = SupplierBill::distinct()->pluck('period_year')->sort()->reverse();
        
        // Summary
        $totalUnpaid = SupplierBill::where('status', 'belum_lunas')->sum('total_amount');
        $totalPaid = SupplierBill::where('status', 'lunas')->sum('total_amount');
        
        return view('supplier-bills.index', compact('bills', 'suppliers', 'years', 'totalUnpaid', 'totalPaid'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('supplier-bills.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'total_amount' => 'required|numeric|min:0',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2100',
            'status' => 'required|in:belum_lunas,lunas',
            'notes' => 'nullable|string',
        ]);
        
        if ($validated['status'] === 'lunas') {
            $validated['paid_at'] = now();
        }
        
        SupplierBill::create($validated);
        
        return redirect()->route('supplier-bills.index')
            ->with('success', 'Tagihan supplier berhasil ditambahkan.');
    }
    
    /**
     * Generate bill from incoming goods
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2100',
        ]);
        
        // Check if bill already exists
        $existingBill = SupplierBill::where('supplier_id', $validated['supplier_id'])
            ->where('period_month', $validated['period_month'])
            ->where('period_year', $validated['period_year'])
            ->first();
            
        if ($existingBill) {
            return back()->withErrors(['supplier_id' => 'Tagihan untuk periode ini sudah ada.'])->withInput();
        }
        
        // Calculate total from incoming goods
        $total = IncomingGood::where('supplier_id', $validated['supplier_id'])
            ->whereMonth('date', $validated['period_month'])
            ->whereYear('date', $validated['period_year'])
            ->sum('total_price');
            
        if ($total <= 0) {
            return back()->withErrors(['supplier_id' => 'Tidak ada transaksi untuk periode ini.'])->withInput();
        }
        
        SupplierBill::create([
            'supplier_id' => $validated['supplier_id'],
            'total_amount' => $total,
            'period_month' => $validated['period_month'],
            'period_year' => $validated['period_year'],
            'status' => 'belum_lunas',
        ]);
        
        return redirect()->route('supplier-bills.index')
            ->with('success', 'Tagihan berhasil di-generate dari data barang masuk.');
    }

    public function show(SupplierBill $supplierBill)
    {
        $supplierBill->load('supplier');
        
        // Get related incoming goods
        $incomingGoods = IncomingGood::with('item')
            ->where('supplier_id', $supplierBill->supplier_id)
            ->whereMonth('date', $supplierBill->period_month)
            ->whereYear('date', $supplierBill->period_year)
            ->get();
            
        return view('supplier-bills.show', compact('supplierBill', 'incomingGoods'));
    }

    public function edit(SupplierBill $supplierBill)
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('supplier-bills.edit', compact('supplierBill', 'suppliers'));
    }

    public function update(Request $request, SupplierBill $supplierBill)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'total_amount' => 'required|numeric|min:0',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2020|max:2100',
            'status' => 'required|in:belum_lunas,lunas',
            'notes' => 'nullable|string',
        ]);
        
        if ($validated['status'] === 'lunas' && $supplierBill->status !== 'lunas') {
            $validated['paid_at'] = now();
        } elseif ($validated['status'] === 'belum_lunas') {
            $validated['paid_at'] = null;
        }
        
        $supplierBill->update($validated);
        
        return redirect()->route('supplier-bills.index')
            ->with('success', 'Tagihan supplier berhasil diperbarui.');
    }
    
    /**
     * Mark bill as paid
     */
    public function markAsPaid(SupplierBill $supplierBill)
    {
        $supplierBill->markAsPaid();
        
        return back()->with('success', 'Tagihan telah ditandai sebagai lunas.');
    }

    /**
     * Recalculate bill total from incoming goods
     */
    public function recalculate(SupplierBill $supplierBill)
    {
        if ($supplierBill->status === 'lunas') {
            return back()->with('error', 'Tagihan yang sudah lunas tidak dapat dihitung ulang.');
        }

        $total = IncomingGood::where('supplier_id', $supplierBill->supplier_id)
            ->whereMonth('date', $supplierBill->period_month)
            ->whereYear('date', $supplierBill->period_year)
            ->sum('total_price');

        $supplierBill->update(['total_amount' => $total]);

        return back()->with('success', 'Total tagihan berhasil dihitung ulang: Rp ' . number_format($total, 0, ',', '.'));
    }

    public function destroy(SupplierBill $supplierBill)
    {
        $supplierBill->delete();
        
        return redirect()->route('supplier-bills.index')
            ->with('success', 'Tagihan supplier berhasil dihapus.');
    }
}
