<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\IncomingGoodController;
use App\Http\Controllers\OutgoingGoodController;
use App\Http\Controllers\SupplierBillController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Items (Stok Barang)
    Route::resource('items', ItemController::class);
    
    // Suppliers
    Route::resource('suppliers', SupplierController::class);
    
    // Menus (Resep)
    Route::resource('menus', MenuController::class);
    
    // Incoming Goods (Barang Masuk)
    Route::resource('incoming-goods', IncomingGoodController::class);
    
    // Outgoing Goods (Barang Keluar)
    Route::post('outgoing-goods/from-menu', [OutgoingGoodController::class, 'storeFromMenu'])->name('outgoing-goods.from-menu');
    Route::resource('outgoing-goods', OutgoingGoodController::class);
    
    // Supplier Bills (Tagihan Supplier)
    Route::post('supplier-bills/generate', [SupplierBillController::class, 'generate'])->name('supplier-bills.generate');
    Route::patch('supplier-bills/{supplierBill}/mark-paid', [SupplierBillController::class, 'markAsPaid'])->name('supplier-bills.mark-paid');
    Route::post('supplier-bills/{supplierBill}/recalculate', [SupplierBillController::class, 'recalculate'])->name('supplier-bills.recalculate');
    Route::resource('supplier-bills', SupplierBillController::class);
    
    // Reports (Laporan)
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/weekly', [ReportController::class, 'weekly'])->name('reports.weekly');
    Route::get('reports/weekly/export-excel', [ReportController::class, 'weeklyExportExcel'])->name('reports.weekly.export-excel');
    Route::get('reports/weekly/export-pdf', [ReportController::class, 'weeklyExportPdf'])->name('reports.weekly.export-pdf');
    Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('reports/monthly/export-excel', [ReportController::class, 'monthlyExportExcel'])->name('reports.monthly.export-excel');
    Route::get('reports/monthly/export-pdf', [ReportController::class, 'monthlyExportPdf'])->name('reports.monthly.export-pdf');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
