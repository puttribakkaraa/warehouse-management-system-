<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WeeklyReportExport implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate, $endDate)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $rows = [];
        
        // Summary Section
        $rows[] = ['RINGKASAN LAPORAN MINGGUAN'];
        $rows[] = ['Periode', $this->startDate->format('d/m/Y') . ' - ' . $this->endDate->format('d/m/Y')];
        $rows[] = ['Total Barang Masuk', $this->data['totalIncoming']];
        $rows[] = ['Total Barang Keluar', $this->data['totalOutgoing']];
        $rows[] = ['Total Nilai Pembelian', 'Rp ' . number_format($this->data['totalIncomingValue'], 0, ',', '.')];
        $rows[] = [];
        
        // Incoming by Item
        $rows[] = ['BARANG MASUK PER ITEM'];
        $rows[] = ['Nama Barang', 'Jumlah', 'Satuan', 'Nilai'];
        foreach ($this->data['incomingByItem'] as $item) {
            $rows[] = [
                $item['item']->name,
                $item['total_quantity'],
                $item['item']->unit,
                'Rp ' . number_format($item['total_value'], 0, ',', '.')
            ];
        }
        $rows[] = [];
        
        // Outgoing by Item
        $rows[] = ['BARANG KELUAR PER ITEM'];
        $rows[] = ['Nama Barang', 'Jumlah', 'Satuan'];
        foreach ($this->data['outgoingByItem'] as $item) {
            $rows[] = [
                $item['item']->name,
                $item['total_quantity'],
                $item['item']->unit
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
        return 'Laporan Mingguan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}
