@extends('layouts.wms')

@section('title', 'Laporan Bulanan')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar-month me-2"></i>Filter Periode</span>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.monthly.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                </a>
                <a href="{{ route('reports.monthly.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="year" class="form-select">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Lihat Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @php
        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    @endphp

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Menampilkan laporan untuk <strong>{{ $months[$month] }} {{ $year }}</strong>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card success">
                <i class="bi bi-arrow-down-circle stat-icon"></i>
                <div class="stat-value">{{ number_format($totalIncoming, 0) }}</div>
                <div class="stat-label">Total Barang Masuk</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card info">
                <i class="bi bi-arrow-up-circle stat-icon"></i>
                <div class="stat-value">{{ number_format($totalOutgoing, 0) }}</div>
                <div class="stat-label">Total Barang Keluar</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card primary">
                <i class="bi bi-cash stat-icon"></i>
                <div class="stat-value">Rp {{ number_format($totalIncomingValue, 0, ',', '.') }}</div>
                <div class="stat-label">Nilai Pembelian</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card warning">
                <i class="bi bi-receipt stat-icon"></i>
                <div class="stat-value">Rp {{ number_format($totalBills, 0, ',', '.') }}</div>
                <div class="stat-label">Total Tagihan</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pembelian per Supplier -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-truck me-2"></i>Pembelian per Supplier
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Qty</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incomingBySupplier as $data)
                                    <tr>
                                        <td>{{ $data['supplier']->name }}</td>
                                        <td>{{ number_format($data['total_quantity'], 0) }}</td>
                                        <td>Rp {{ number_format($data['total_value'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pemakaian per Menu -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-book me-2"></i>Pemakaian per Menu
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Total Pemakaian Bahan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outgoingByMenu as $data)
                                    <tr>
                                        <td>{{ $data['menu']->name }}</td>
                                        <td>{{ number_format($data['total_quantity'], 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-3">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tagihan Supplier -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-receipt me-2"></i>Tagihan Supplier Bulan Ini
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Total Tagihan</th>
                            <th>Status</th>
                            <th>Tgl Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplierBills as $bill)
                            <tr>
                                <td>{{ $bill->supplier->name }}</td>
                                <td>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</td>
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
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Tidak ada tagihan</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($supplierBills->count() > 0)
                        <tfoot>
                            <tr class="table-light">
                                <td><strong>Total</strong></td>
                                <td><strong>Rp {{ number_format($totalBills, 0, ',', '.') }}</strong></td>
                                <td colspan="2">
                                    <span class="text-warning">Belum Lunas: Rp {{ number_format($unpaidBills, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Stock Summary -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-box me-2"></i>Stok Akhir Bulan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Barang</th>
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
            </div>
        </div>
    </div>
@endsection
