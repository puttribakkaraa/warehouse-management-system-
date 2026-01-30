@extends('layouts.wms')

@section('title', 'Laporan Mingguan')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-calendar-week me-2"></i>Filter Periode</span>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.weekly.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                </a>
                <a href="{{ route('reports.weekly.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Awal Minggu</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Lihat Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Menampilkan laporan untuk periode <strong>{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</strong>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card success">
                <i class="bi bi-arrow-down-circle stat-icon"></i>
                <div class="stat-value">{{ number_format($totalIncoming, 0) }}</div>
                <div class="stat-label">Total Barang Masuk</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card info">
                <i class="bi bi-arrow-up-circle stat-icon"></i>
                <div class="stat-value">{{ number_format($totalOutgoing, 0) }}</div>
                <div class="stat-label">Total Barang Keluar</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card primary">
                <i class="bi bi-cash stat-icon"></i>
                <div class="stat-value">Rp {{ number_format($totalIncomingValue, 0, ',', '.') }}</div>
                <div class="stat-label">Total Nilai Pembelian</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Barang Masuk per Item -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-arrow-down-circle text-success me-2"></i>Ringkasan Barang Masuk
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incomingByItem as $data)
                                    <tr>
                                        <td>{{ $data['item']->name }}</td>
                                        <td>{{ number_format($data['total_quantity'], 0) }} {{ $data['item']->unit }}</td>
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

        <!-- Barang Keluar per Item -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-arrow-up-circle text-info me-2"></i>Ringkasan Barang Keluar
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outgoingByItem as $data)
                                    <tr>
                                        <td>{{ $data['item']->name }}</td>
                                        <td>{{ number_format($data['total_quantity'], 0) }} {{ $data['item']->unit }}</td>
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

    <!-- Stock Summary -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-box me-2"></i>Stok Akhir Minggu Ini
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
