@extends('layouts.wms')

@section('title', 'Barang Keluar')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-arrow-up-circle me-2"></i>Daftar Barang Keluar</span>
            <a href="{{ route('outgoing-goods.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Barang Keluar
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
                    <label class="form-label">Menu</label>
                    <select name="menu_id" class="form-select">
                        <option value="">Semua Menu</option>
                        @foreach($menus as $menu)
                            <option value="{{ $menu->id }}" {{ request('menu_id') == $menu->id ? 'selected' : '' }}>
                                {{ $menu->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['start_date', 'end_date', 'menu_id']))
                        <a href="{{ route('outgoing-goods.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                            <th>Menu</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($outgoingGoods as $index => $outgoing)
                            <tr>
                                <td>{{ $outgoingGoods->firstItem() + $index }}</td>
                                <td>{{ $outgoing->date->format('d/m/Y') }}</td>
                                <td>{{ $outgoing->item->name }}</td>
                                <td>{{ number_format($outgoing->quantity, 0) }} {{ $outgoing->item->unit }}</td>
                                <td>{{ $outgoing->menu->name ?? '-' }}</td>
                                <td>{{ Str::limit($outgoing->notes, 30) ?? '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('outgoing-goods.edit', $outgoing) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('outgoing-goods.destroy', $outgoing) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus? Stok akan dikembalikan.')">
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
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data barang keluar</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $outgoingGoods->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
