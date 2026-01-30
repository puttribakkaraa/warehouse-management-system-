@extends('layouts.wms')

@section('title', 'Detail Tagihan')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-receipt me-2"></i>Informasi Tagihan
                </div>
                <div class="card-body">
                    <h4>{{ $supplierBill->supplier->name }}</h4>
                    <p class="text-muted">Periode: {{ $supplierBill->period_label }}</p>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <strong>Total Tagihan:</strong>
                        <div class="fs-3 fw-bold text-primary">Rp {{ number_format($supplierBill->total_amount, 0, ',', '.') }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Status:</strong>
                        @if($supplierBill->status == 'lunas')
                            <span class="badge badge-success">Lunas</span>
                        @else
                            <span class="badge badge-warning">Belum Lunas</span>
                        @endif
                    </div>
                    
                    @if($supplierBill->paid_at)
                        <div class="mb-3">
                            <strong>Tanggal Bayar:</strong> {{ $supplierBill->paid_at->format('d/m/Y H:i') }}
                        </div>
                    @endif
                    
                    @if($supplierBill->notes)
                        <div class="mb-3">
                            <strong>Catatan:</strong><br>
                            {{ $supplierBill->notes }}
                        </div>
                    @endif

                    @php
                        $realTotal = $incomingGoods->sum('total_price');
                        $storedTotal = $supplierBill->total_amount;
                        $hasDifference = $realTotal != $storedTotal;
                    @endphp

                    @if($hasDifference && $supplierBill->status == 'belum_lunas')
                        <div class="alert alert-warning mb-3">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                                <div>
                                    <strong>Selisih Total!</strong><br>
                                    Total Aktual: Rp {{ number_format($realTotal, 0, ',', '.') }}<br>
                                    <small>Total tagihan perlu diupdate.</small>
                                </div>
                            </div>
                            <form action="{{ route('supplier-bills.recalculate', $supplierBill) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Hitung Ulang & Update
                                </button>
                            </form>
                        </div>
                    @endif
                    
                    <hr>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        @if($supplierBill->status == 'belum_lunas')
                            <form action="{{ route('supplier-bills.mark-paid', $supplierBill) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success" onclick="return confirm('Tandai sebagai lunas?')">
                                    <i class="bi bi-check-lg me-1"></i>Tandai Lunas
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('supplier-bills.edit', $supplierBill) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('supplier-bills.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-list-ul me-2"></i>Detail Pembelian Periode Ini
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incomingGoods as $incoming)
                                    <tr>
                                        <td>{{ $incoming->date->format('d/m/Y') }}</td>
                                        <td>{{ $incoming->item->name }}</td>
                                        <td>{{ $incoming->quantity }} {{ $incoming->item->unit }}</td>
                                        <td>Rp {{ number_format($incoming->unit_price, 0, ',', '.') }}</td>
                                        <td><strong>Rp {{ number_format($incoming->total_price, 0, ',', '.') }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Tidak ada data pembelian untuk periode ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($incomingGoods->count() > 0)
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>Rp {{ number_format($incomingGoods->sum('total_price'), 0, ',', '.') }}</strong></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
