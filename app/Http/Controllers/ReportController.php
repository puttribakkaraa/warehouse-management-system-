<?php

namespace App\Http\Controllers;

use App\Models\IncomingGood;
use App\Models\OutgoingGood;
use App\Models\Item;
use App\Models\SupplierBill;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\WeeklyReportExport;
use App\Exports\MonthlyReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }
    
    protected function getWeeklyData(Request $request)
    {
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfWeek()
            : Carbon::now()->startOfWeek();
            
        $endDate = $startDate->copy()->endOfWeek();
        
        // Incoming goods
        $incomingGoods = IncomingGood::with(['item', 'supplier'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
            
        $totalIncoming = $incomingGoods->sum('quantity');
        $totalIncomingValue = $incomingGoods->sum('total_price');
        
        // Outgoing goods
        $outgoingGoods = OutgoingGood::with(['item', 'menu'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
            
        $totalOutgoing = $outgoingGoods->sum('quantity');
        
        // Stock summary
        $stockSummary = Item::orderBy('name')->get();
        
        // Group by item
        $incomingByItem = $incomingGoods->groupBy('item_id')->map(function ($items) {
            return [
                'item' => $items->first()->item,
                'total_quantity' => $items->sum('quantity'),
                'total_value' => $items->sum('total_price'),
            ];
        });
        
        $outgoingByItem = $outgoingGoods->groupBy('item_id')->map(function ($items) {
            return [
                'item' => $items->first()->item,
                'total_quantity' => $items->sum('quantity'),
            ];
        });
        
        return compact(
            'startDate',
            'endDate',
            'incomingGoods',
            'outgoingGoods',
            'totalIncoming',
            'totalIncomingValue',
            'totalOutgoing',
            'stockSummary',
            'incomingByItem',
            'outgoingByItem'
        );
    }
    
    protected function getMonthlyData(Request $request)
    {
        $month = $request->filled('month') ? (int)$request->month : Carbon::now()->month;
        $year = $request->filled('year') ? (int)$request->year : Carbon::now()->year;
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        // Incoming goods
        $incomingGoods = IncomingGood::with(['item', 'supplier'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
            
        $totalIncoming = $incomingGoods->sum('quantity');
        $totalIncomingValue = $incomingGoods->sum('total_price');
        
        // Outgoing goods
        $outgoingGoods = OutgoingGood::with(['item', 'menu'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
            
        $totalOutgoing = $outgoingGoods->sum('quantity');
        
        // Supplier bills
        $supplierBills = SupplierBill::with('supplier')
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->get();
            
        $totalBills = $supplierBills->sum('total_amount');
        $unpaidBills = $supplierBills->where('status', 'belum_lunas')->sum('total_amount');
        
        // Stock summary
        $stockSummary = Item::orderBy('name')->get();
        
        // Group by item
        $incomingByItem = $incomingGoods->groupBy('item_id')->map(function ($items) {
            return [
                'item' => $items->first()->item,
                'total_quantity' => $items->sum('quantity'),
                'total_value' => $items->sum('total_price'),
            ];
        });
        
        $outgoingByItem = $outgoingGoods->groupBy('item_id')->map(function ($items) {
            return [
                'item' => $items->first()->item,
                'total_quantity' => $items->sum('quantity'),
            ];
        });
        
        // Group by supplier
        $incomingBySupplier = $incomingGoods->groupBy('supplier_id')->map(function ($items) {
            return [
                'supplier' => $items->first()->supplier,
                'total_quantity' => $items->sum('quantity'),
                'total_value' => $items->sum('total_price'),
            ];
        });
        
        // Group by menu
        $outgoingByMenu = $outgoingGoods->whereNotNull('menu_id')->groupBy('menu_id')->map(function ($items) {
            return [
                'menu' => $items->first()->menu,
                'total_quantity' => $items->sum('quantity'),
            ];
        });
        
        return compact(
            'month',
            'year',
            'startDate',
            'endDate',
            'incomingGoods',
            'outgoingGoods',
            'totalIncoming',
            'totalIncomingValue',
            'totalOutgoing',
            'supplierBills',
            'totalBills',
            'unpaidBills',
            'stockSummary',
            'incomingByItem',
            'outgoingByItem',
            'incomingBySupplier',
            'outgoingByMenu'
        );
    }
    
    public function weekly(Request $request)
    {
        $data = $this->getWeeklyData($request);
        return view('reports.weekly', $data);
    }
    
    public function weeklyExportExcel(Request $request)
    {
        $data = $this->getWeeklyData($request);
        $filename = 'laporan-mingguan-' . $data['startDate']->format('Y-m-d') . '.xlsx';
        
        return Excel::download(
            new WeeklyReportExport($data, $data['startDate'], $data['endDate']),
            $filename
        );
    }
    
    public function weeklyExportPdf(Request $request)
    {
        $data = $this->getWeeklyData($request);
        $filename = 'laporan-mingguan-' . $data['startDate']->format('Y-m-d') . '.pdf';
        
        $pdf = Pdf::loadView('reports.weekly-pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download($filename);
    }
    
    public function monthly(Request $request)
    {
        $data = $this->getMonthlyData($request);
        return view('reports.monthly', $data);
    }
    
    public function monthlyExportExcel(Request $request)
    {
        $data = $this->getMonthlyData($request);
        $filename = 'laporan-bulanan-' . $data['year'] . '-' . str_pad($data['month'], 2, '0', STR_PAD_LEFT) . '.xlsx';
        
        return Excel::download(
            new MonthlyReportExport($data, $data['month'], $data['year']),
            $filename
        );
    }
    
    public function monthlyExportPdf(Request $request)
    {
        $data = $this->getMonthlyData($request);
        $filename = 'laporan-bulanan-' . $data['year'] . '-' . str_pad($data['month'], 2, '0', STR_PAD_LEFT) . '.pdf';
        
        $pdf = Pdf::loadView('reports.monthly-pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->download($filename);
    }
}
