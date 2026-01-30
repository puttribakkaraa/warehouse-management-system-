<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\IncomingGood;
use App\Models\OutgoingGood;
use App\Models\SupplierBill;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Total stok tersedia
        $totalStock = Item::sum('stock_quantity');
        $totalItems = Item::count();
        
        // Barang masuk hari ini
        $todayIncoming = IncomingGood::whereDate('date', Carbon::today())->count();
        $todayIncomingValue = IncomingGood::whereDate('date', Carbon::today())->sum('total_price');
        
        // Barang keluar hari ini
        $todayOutgoing = OutgoingGood::whereDate('date', Carbon::today())->count();
        
        // Tagihan belum dibayar
        $unpaidBills = SupplierBill::where('status', 'belum_lunas')->sum('total_amount');
        $unpaidBillsCount = SupplierBill::where('status', 'belum_lunas')->count();
        
        // Low stock items
        $lowStockItems = Item::whereColumn('stock_quantity', '<=', 'minimum_stock')
            ->where('minimum_stock', '>', 0)
            ->get();
        
        // Recent incoming goods
        $recentIncoming = IncomingGood::with(['item', 'supplier'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
        
        // Recent outgoing goods
        $recentOutgoing = OutgoingGood::with(['item', 'menu'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
        
        // Weekly chart data (last 7 days)
        $weeklyData = $this->getWeeklyChartData();
        
        // Monthly chart data (last 6 months)
        $monthlyData = $this->getMonthlyChartData();
        
        return view('dashboard', compact(
            'totalStock',
            'totalItems',
            'todayIncoming',
            'todayIncomingValue',
            'todayOutgoing',
            'unpaidBills',
            'unpaidBillsCount',
            'lowStockItems',
            'recentIncoming',
            'recentOutgoing',
            'weeklyData',
            'monthlyData'
        ));
    }
    
    private function getWeeklyChartData()
    {
        $labels = [];
        $incoming = [];
        $outgoing = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d/m');
            $incoming[] = IncomingGood::whereDate('date', $date)->sum('quantity');
            $outgoing[] = OutgoingGood::whereDate('date', $date)->sum('quantity');
        }
        
        return [
            'labels' => $labels,
            'incoming' => $incoming,
            'outgoing' => $outgoing,
        ];
    }
    
    private function getMonthlyChartData()
    {
        $labels = [];
        $incoming = [];
        $outgoing = [];
        
        // Fixed year 2026 as requested
        $year = 2026;
        
        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::createFromDate($year, $month, 1);
            $labels[] = $date->format('M Y');
            
            $incoming[] = IncomingGood::whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('quantity');
                
            $outgoing[] = OutgoingGood::whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('quantity');
        }
        
        return [
            'labels' => $labels,
            'incoming' => $incoming,
            'outgoing' => $outgoing,
        ];
    }
}
