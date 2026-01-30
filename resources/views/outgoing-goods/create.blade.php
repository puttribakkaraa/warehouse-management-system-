@extends('layouts.wms')

@section('title', 'Tambah Barang Keluar')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-plus-lg me-2"></i>Input Manual
                </div>
                <div class="card-body">
                    <form action="{{ route('outgoing-goods.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                            <select name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                                <option value="">Pilih Barang</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} (Stok: {{ $item->stock_quantity }} {{ $item->unit }})
                                    </option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                   value="{{ old('quantity') }}" min="0.01" step="0.01" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Menu (Opsional)</label>
                            <select name="menu_id" class="form-select @error('menu_id') is-invalid @enderror">
                                <option value="">Tanpa Menu</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}" {{ old('menu_id') == $menu->id ? 'selected' : '' }}>
                                        {{ $menu->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('menu_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                                   value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan
                            </button>
                            <a href="{{ route('outgoing-goods.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-book me-2"></i>Input dari Menu
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Otomatis mengurangi semua bahan sesuai resep menu</p>
                    
                    <form action="{{ route('outgoing-goods.from-menu') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Pilih Menu <span class="text-danger">*</span></label>
                            <select name="menu_id" class="form-select" required>
                                <option value="">Pilih Menu</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah Porsi <span class="text-danger">*</span></label>
                            <input type="number" name="multiplier" class="form-control" value="1" min="1" required>
                            <small class="text-muted">Bahan akan dikalikan sesuai jumlah porsi</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
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
