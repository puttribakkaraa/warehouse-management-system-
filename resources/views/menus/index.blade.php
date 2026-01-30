@extends('layouts.wms')

@section('title', 'Menu / Resep')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-book me-2"></i>Daftar Menu / Resep</span>
            <a href="{{ route('menus.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Tambah Menu
            </a>
        </div>
        <div class="card-body">
            <!-- Search -->
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama menu..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Menu</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Bahan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $index => $menu)
                            <tr>
                                <td>{{ $menus->firstItem() + $index }}</td>
                                <td><strong>{{ $menu->name }}</strong></td>
                                <td>{{ Str::limit($menu->description, 50) ?? '-' }}</td>
                                <td><span class="badge badge-info">{{ $menu->ingredients_count }} bahan</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('menus.show', $menu) }}" class="btn btn-outline-primary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('menus.edit', $menu) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus menu ini?')">
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
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data menu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $menus->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
