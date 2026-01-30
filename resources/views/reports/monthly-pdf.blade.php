<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4f46e5; color: white; }
        .section-title { background: #f3f4f6; font-weight: bold; padding: 10px; margin-top: 15px; }
        .summary-box { background: #f3f4f6; padding: 15px; margin-bottom: 20px; }
        .summary-item { display: inline-block; margin-right: 20px; margin-bottom: 10px; }
        .summary-label { color: #666; font-size: 11px; }
        .summary-value { font-size: 14px; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 10px; }
        .badge-success { background: #d1fae5; color: #059669; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    @php
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    @endphp

    <div class="header">
        <h1>LAPORAN BULANAN</h1>
        <p>Periode: {{ $months[$month] }} {{ $year }}</p>
        <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-label">Total Barang Masuk</div>
            <div class="summary-value">{{ number_format($totalIncoming, 0) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Barang Keluar</div>
            <div class="summary-value">{{ number_format($totalOutgoing, 0) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Nilai Pembelian</div>
            <div class="summary-value">Rp {{ number_format($totalIncomingValue, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Tagihan</div>
            <div class="summary-value">Rp {{ number_format($totalBills, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Belum Lunas</div>
            <div class="summary-value" style="color: #dc2626;">Rp {{ number_format($unpaidBills, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="section-title">Pembelian per Supplier</div>
    <table>
        <thead>
            <tr>
                <th>Nama Supplier</th>
                <th>Jumlah</th>
                <th class="text-right">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomingBySupplier as $data)
                <tr>
                    <td>{{ $data['supplier']->name }}</td>
                    <td>{{ number_format($data['total_quantity'], 0) }}</td>
                    <td class="text-right">Rp {{ number_format($data['total_value'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align: center;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Pemakaian per Menu</div>
    <table>
        <thead>
            <tr>
                <th>Nama Menu</th>
                <th>Total Pemakaian</th>
            </tr>
        </thead>
        <tbody>
            @forelse($outgoingByMenu as $data)
                <tr>
                    <td>{{ $data['menu']->name }}</td>
                    <td>{{ number_format($data['total_quantity'], 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="2" style="text-align: center;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Tagihan Supplier</div>
    <table>
        <thead>
            <tr>
                <th>Supplier</th>
                <th class="text-right">Total Tagihan</th>
                <th>Status</th>
                <th>Tanggal Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($supplierBills as $bill)
                <tr>
                    <td>{{ $bill->supplier->name }}</td>
                    <td class="text-right">Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</td>
                    <td>
                        @if($bill->status == 'lunas')
                            <span class="badge badge-success">Lunas</span>
                        @else
                            <span class="badge badge-warning">Belum Lunas</span>
                        @endif
                    </td>
                    <td>{{ $bill->paid_at ? $bill->paid_at->format('d/m/Y') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align: center;">Tidak ada tagihan</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Stok Akhir</div>
    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Stok</th>
                <th>Min. Stok</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockSummary as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ number_format($item->stock_quantity, 0) }}</td>
                    <td>{{ number_format($item->minimum_stock, 0) }}</td>
                    <td>
                        @if($item->stock_status == 'habis')
                            <span class="badge badge-danger">Habis</span>
                        @elseif($item->stock_status == 'hampir_habis')
                            <span class="badge badge-warning">Hampir Habis</span>
                        @else
                            <span class="badge badge-success">Aman</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
