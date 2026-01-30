@extends('layouts.wms')

@section('title', 'Detail Menu')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-book me-2"></i>Informasi Menu
                </div>
                <div class="card-body">
                    <h4>{{ $menu->name }}</h4>
                    <p class="text-muted">{{ $menu->description ?? 'Tidak ada deskripsi' }}</p>
                    
                    <hr>
                    
                    <h6 class="mb-3"><i class="bi bi-list-check me-2"></i>Bahan yang Dibutuhkan:</h6>
                    
                    @if($menu->ingredients->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama Bahan</th>
                                        <th>Jumlah</th>
                                        <th>Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($menu->ingredients as $ingredient)
                                        <tr>
                                            <td>{{ $ingredient->name }}</td>
                                            <td>{{ $ingredient->pivot->quantity_required }} {{ $ingredient->unit }}</td>
                                            <td>
                                                @if($ingredient->stock_quantity >= $ingredient->pivot->quantity_required)
                                                    <span class="badge badge-success">{{ $ingredient->stock_quantity }} {{ $ingredient->unit }}</span>
                                                @else
                                                    <span class="badge badge-danger">{{ $ingredient->stock_quantity }} {{ $ingredient->unit }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Belum ada bahan yang ditentukan</p>
                    @endif
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('menus.edit', $menu) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('menus.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-arrow-up-circle me-2"></i>Gunakan Menu Ini
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Buat barang keluar otomatis berdasarkan resep menu ini</p>
                    
                    <form action="{{ route('outgoing-goods.from-menu') }}" method="POST">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah Porsi</label>
                            <input type="number" name="multiplier" class="form-control" value="1" min="1" required>
                            <small class="text-muted">Bahan akan dikalikan sesuai jumlah porsi</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pemakaian</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan opsional"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>Gunakan Menu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
