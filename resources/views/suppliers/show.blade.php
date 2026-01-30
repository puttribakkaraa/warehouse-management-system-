@extends('layouts.wms')

@section('title', 'Detail Supplier')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-truck me-2"></i>Informasi Supplier
                </div>
                <div class="card-body">
                    <h4>{{ $supplier->name }}</h4>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <i class="bi bi-telephone me-2 text-muted"></i>
                        <strong>Telepon:</strong> {{ $supplier->phone ?? '-' }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="bi bi-envelope me-2 text-muted"></i>
                        <strong>Email:</strong> {{ $supplier->email ?? '-' }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="bi bi-geo-alt me-2 text-muted"></i>
                        <strong>Alamat:</strong><br>
                        {{ $supplier->address ?? '-' }}
                    </div>
                    
                    @if($supplier->notes)
                        <div class="mb-3">
                            <i class="bi bi-sticky me-2 text-muted"></i>
                            <strong>Catatan:</strong><br>
                            {{ $supplier->notes }}
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Recent Purchases -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-arrow-down-circle text-success me-2"></i>Riwayat Pembelian Terakhir
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplier->incomingGoods->take(10) as $incoming)
                                    <tr>
                                        <td>{{ $incoming->date->format('d/m/Y') }}</td>
                                        <td>{{ $incoming->item->name }}</td>
                                        <td>{{ $incoming->quantity }} {{ $incoming->item->unit }}</td>
                                        <td>Rp {{ number_format($incoming->total_price, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Belum ada riwayat pembelian</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Bills -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-receipt me-2"></i>Tagihan
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplier->bills->take(10) as $bill)
                                    <tr>
                                        <td>{{ $bill->period_label }}</td>
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
                                        <td colspan="4" class="text-center text-muted py-3">Belum ada tagihan</td>
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
