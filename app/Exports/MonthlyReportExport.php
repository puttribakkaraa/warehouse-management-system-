<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyReportExport implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $month;
    protected $year;

    public function __construct($data, $month, $year)
    {
        $this->data = $data;
        $this->month = $month;
        $this->year = $year;
    }

    public function array(): array
    {
        $rows = [];
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        // Summary Section
        $rows[] = ['RINGKASAN LAPORAN BULANAN'];
        $rows[] = ['Periode', $months[$this->month] . ' ' . $this->year];
        $rows[] = ['Total Barang Masuk', $this->data['totalIncoming']];
        $rows[] = ['Total Barang Keluar', $this->data['totalOutgoing']];
        $rows[] = ['Total Nilai Pembelian', 'Rp ' . number_format($this->data['totalIncomingValue'], 0, ',', '.')];
        $rows[] = ['Total Tagihan', 'Rp ' . number_format($this->data['totalBills'], 0, ',', '.')];
        $rows[] = ['Tagihan Belum Lunas', 'Rp ' . number_format($this->data['unpaidBills'], 0, ',', '.')];
        $rows[] = [];
        
        // Incoming by Supplier
        $rows[] = ['PEMBELIAN PER SUPPLIER'];
        $rows[] = ['Nama Supplier', 'Jumlah', 'Nilai'];
        foreach ($this->data['incomingBySupplier'] as $item) {
            $rows[] = [
                $item['supplier']->name,
                $item['total_quantity'],
                'Rp ' . number_format($item['total_value'], 0, ',', '.')
            ];
        }
        $rows[] = [];
        
        // Outgoing by Menu
        $rows[] = ['PEMAKAIAN PER MENU'];
        $rows[] = ['Nama Menu', 'Total Pemakaian'];
        foreach ($this->data['outgoingByMenu'] as $item) {
            $rows[] = [
                $item['menu']->name,
                $item['total_quantity']
            ];
        }
        $rows[] = [];
        
        // Supplier Bills
        $rows[] = ['TAGIHAN SUPPLIER'];
        $rows[] = ['Supplier', 'Total Tagihan', 'Status', 'Tanggal Bayar'];
        foreach ($this->data['supplierBills'] as $bill) {
            $rows[] = [
                $bill->supplier->name,
                'Rp ' . number_format($bill->total_amount, 0, ',', '.'),
                $bill->status == 'lunas' ? 'Lunas' : 'Belum Lunas',
                $bill->paid_at ? $bill->paid_at->format('d/m/Y') : '-'
            ];
        }
        $rows[] = [];
        
        // Stock Summary
        $rows[] = ['STOK AKHIR'];
        $rows[] = ['Nama Barang', 'Satuan', 'Stok', 'Min. Stok', 'Status'];
        foreach ($this->data['stockSummary'] as $item) {
            $status = $item->stock_status == 'habis' ? 'Habis' : ($item->stock_status == 'hampir_habis' ? 'Hampir Habis' : 'Aman');
            $rows[] = [
                $item->name,
                $item->unit,
                $item->stock_quantity,
                $item->minimum_stock,
                $status
            ];
        }
        
        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Laporan Bulanan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
