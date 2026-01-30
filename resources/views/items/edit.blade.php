@extends('layouts.wms')

@section('title', 'Edit Barang')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="bi bi-pencil me-2"></i>Edit Barang: {{ $item->name }}
        </div>
        <div class="card-body">
            <form action="{{ route('items.update', $item) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $item->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Satuan <span class="text-danger">*</span></label>
                        <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" 
                               value="{{ old('unit', $item->unit) }}" required>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Stok Saat Ini</label>
                        <input type="text" class="form-control" value="{{ $item->stock_quantity }} {{ $item->unit }}" disabled>
                        <small class="text-muted">Stok diubah melalui transaksi barang masuk/keluar</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Minimum Stok</label>
                        <input type="number" name="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" 
                               value="{{ old('minimum_stock', $item->minimum_stock) }}" min="0" step="0.01">
                        @error('minimum_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $item->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
