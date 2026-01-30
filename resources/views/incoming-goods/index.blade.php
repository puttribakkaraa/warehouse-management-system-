@extends('layouts.wms')

@section('title', 'Barang Masuk')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-arrow-down-circle me-2"></i>Daftar Barang Masuk</span>
            <a href="{{ route('incoming-goods.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Barang Masuk
            </a>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['start_date', 'end_date', 'supplier_id']))
                        <a href="{{ route('incoming-goods.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Total</th>
                            <th>Supplier</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomingGoods as $index => $incoming)
                            <tr>
                                <td>{{ $incomingGoods->firstItem() + $index }}</td>
                                <td>{{ $incoming->date->format('d/m/Y') }}</td>
                                <td>{{ $incoming->item->name }}</td>
                                <td>{{ number_format($incoming->quantity, 0) }} {{ $incoming->item->unit }}</td>
                                <td>Rp {{ number_format($incoming->unit_price, 0, ',', '.') }}</td>
                                <td><strong>Rp {{ number_format($incoming->total_price, 0, ',', '.') }}</strong></td>
                                <td>{{ $incoming->supplier->name }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('incoming-goods.edit', $incoming) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('incoming-goods.destroy', $incoming) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus? Stok akan dikurangi.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data barang masuk</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $incomingGoods->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
