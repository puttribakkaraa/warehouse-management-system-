@extends('layouts.wms')

@section('title', 'Detail Barang')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-box me-2"></i>Informasi Barang
                </div>
                <div class="card-body">
                    <h4>{{ $item->name }}</h4>
                    <p class="text-muted">{{ $item->description ?? 'Tidak ada deskripsi' }}</p>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <strong>Satuan:</strong> {{ $item->unit }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>Stok Saat Ini:</strong>
                        <span class="fs-4 fw-bold ms-2">{{ number_format($item->stock_quantity, 0) }} {{ $item->unit }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Minimum Stok:</strong> {{ number_format($item->minimum_stock, 0) }} {{ $item->unit }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>Status:</strong>
                        @if($item->stock_status == 'habis')
                            <span class="badge badge-danger">Habis</span>
                        @elseif($item->stock_status == 'hampir_habis')
                            <span class="badge badge-warning">Hampir Habis</span>
                        @else
                            <span class="badge badge-success">Aman</span>
                        @endif
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('items.edit', $item) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Recent Incoming -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-arrow-down-circle text-success me-2"></i>Riwayat Barang Masuk Terakhir
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Supplier</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item->incomingGoods->take(5) as $incoming)
                                    <tr>
                                        <td>{{ $incoming->date->format('d/m/Y') }}</td>
                                        <td>{{ $incoming->quantity }} {{ $item->unit }}</td>
                                        <td>{{ $incoming->supplier->name }}</td>
                                        <td>Rp {{ number_format($incoming->total_price, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Belum ada riwayat</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Recent Outgoing -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-arrow-up-circle text-info me-2"></i>Riwayat Barang Keluar Terakhir
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Menu</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item->outgoingGoods->take(5) as $outgoing)
                                    <tr>
                                        <td>{{ $outgoing->date->format('d/m/Y') }}</td>
                                        <td>{{ $outgoing->quantity }} {{ $item->unit }}</td>
                                        <td>{{ $outgoing->menu->name ?? '-' }}</td>
                                        <td>{{ $outgoing->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Belum ada riwayat</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
