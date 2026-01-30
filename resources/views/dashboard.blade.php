@extends('layouts.wms')

@section('title', 'Dashboard')

@section('content')
    <!-- Low Stock Alert -->
    @if($lowStockItems->count() > 0)
        <div class="low-stock-alert d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill text-warning me-3" style="font-size: 1.5rem;"></i>
            <div>
                <strong>Peringatan Stok Menipis!</strong>
                <span class="ms-2">{{ $lowStockItems->count() }} barang memiliki stok di bawah batas minimum.</span>
                <a href="{{ route('items.index', ['status' => 'hampir_habis']) }}" class="ms-2">Lihat Detail →</a>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card primary">
                <i class="bi bi-box stat-icon"></i>
                <div class="stat-value">{{ number_format($totalItems) }}</div>
                <div class="stat-label">Jenis Barang</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card success">
                <i class="bi bi-arrow-down-circle stat-icon"></i>
                <div class="stat-value">{{ number_format($todayIncoming) }}</div>
                <div class="stat-label">Barang Masuk Hari Ini</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card info">
                <i class="bi bi-arrow-up-circle stat-icon"></i>
                <div class="stat-value">{{ number_format($todayOutgoing) }}</div>
                <div class="stat-label">Barang Keluar Hari Ini</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card danger">
                <i class="bi bi-receipt stat-icon"></i>
                <div class="stat-value">Rp {{ number_format($unpaidBills, 0, ',', '.') }}</div>
                <div class="stat-label">Tagihan Belum Lunas ({{ $unpaidBillsCount }})</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-graph-up me-2"></i>Grafik Mingguan</span>
                </div>
                <div class="card-body">
                    <canvas id="weeklyChart" height="180"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-bar-chart me-2"></i>Grafik Bulanan</span>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Row -->
    <div class="row g-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-arrow-down-circle me-2 text-success"></i>Barang Masuk Terbaru</span>
                    <a href="{{ route('incoming-goods.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentIncoming as $incoming)
                                    <tr>
                                        <td>{{ $incoming->date->format('d/m/Y') }}</td>
                                        <td>{{ $incoming->item->name }}</td>
                                        <td>{{ number_format($incoming->quantity, 0) }} {{ $incoming->item->unit }}</td>
                                        <td>{{ $incoming->supplier->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data barang masuk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-arrow-up-circle me-2 text-info"></i>Barang Keluar Terbaru</span>
                    <a href="{{ route('outgoing-goods.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Menu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOutgoing as $outgoing)
                                    <tr>
                                        <td>{{ $outgoing->date->format('d/m/Y') }}</td>
                                        <td>{{ $outgoing->item->name }}</td>
                                        <td>{{ number_format($outgoing->quantity, 0) }} {{ $outgoing->item->unit }}</td>
                                        <td>{{ $outgoing->menu->name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data barang keluar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Items -->
    @if($lowStockItems->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <i class="bi bi-exclamation-triangle text-warning me-2"></i>Barang dengan Stok Menipis
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Barang</th>
                                <th>Stok Saat Ini</th>
                                <th>Batas Minimum</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->stock_quantity }} {{ $item->unit }}</td>
                                    <td>{{ $item->minimum_stock }} {{ $item->unit }}</td>
                                    <td>
                                        @if($item->stock_quantity <= 0)
                                            <span class="badge badge-danger">Habis</span>
                                        @else
                                            <span class="badge badge-warning">Hampir Habis</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    // Weekly Chart
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($weeklyData['labels']) !!},
            datasets: [{
                label: 'Barang Masuk',
                data: {!! json_encode($weeklyData['incoming']) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Barang Keluar',
                data: {!! json_encode($weeklyData['outgoing']) !!},
                borderColor: '#06b6d4',
                backgroundColor: 'rgba(6, 182, 212, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyData['labels']) !!},
            datasets: [{
                label: 'Barang Masuk',
                data: {!! json_encode($monthlyData['incoming']) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 5
            }, {
                label: 'Barang Keluar',
                data: {!! json_encode($monthlyData['outgoing']) !!},
                backgroundColor: 'rgba(6, 182, 212, 0.8)',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
