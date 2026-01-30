@extends('layouts.wms')

@section('title', 'Tambah Menu')

@section('content')
    <div class="card">
        <div class="card-header">
            <i class="bi bi-plus-lg me-2"></i>Tambah Menu Baru
        </div>
        <div class="card-body">
            <form action="{{ route('menus.store') }}" method="POST" id="menuForm">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Menu <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required placeholder="Contoh: Nasi Goreng, Jus Mangga">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Deskripsi</label>
                        <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" 
                               value="{{ old('description') }}" placeholder="Keterangan menu (opsional)">
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <hr>
                
                <h5 class="mb-3"><i class="bi bi-list-check me-2"></i>Bahan-bahan (Ingredients)</h5>
                
                <div id="ingredientsContainer">
                    <div class="ingredient-row row mb-2">
                        <div class="col-md-5">
                            <select name="ingredients[0][item_id]" class="form-select" required>
                                <option value="">Pilih Barang</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="ingredients[0][quantity_required]" class="form-control" 
                                   placeholder="Jumlah diperlukan" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-danger btn-remove-ingredient" disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-outline-primary mb-3" id="addIngredient">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Bahan
                </button>
                
                <hr>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                    <a href="{{ route('menus.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let ingredientIndex = 1;
    const items = @json($items);
    
    document.getElementById('addIngredient').addEventListener('click', function() {
        const container = document.getElementById('ingredientsContainer');
        const html = `
            <div class="ingredient-row row mb-2">
                <div class="col-md-5">
                    <select name="ingredients[${ingredientIndex}][item_id]" class="form-select" required>
                        <option value="">Pilih Barang</option>
                        ${items.map(item => `<option value="${item.id}">${item.name} (${item.unit})</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="ingredients[${ingredientIndex}][quantity_required]" class="form-control" 
                           placeholder="Jumlah diperlukan" step="0.01" min="0.01" required>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger btn-remove-ingredient">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        ingredientIndex++;
        updateRemoveButtons();
    });
    
    document.getElementById('ingredientsContainer').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-ingredient')) {
            e.target.closest('.ingredient-row').remove();
            updateRemoveButtons();
        }
    });
    
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.ingredient-row');
        rows.forEach((row, index) => {
            const btn = row.querySelector('.btn-remove-ingredient');
            btn.disabled = rows.length === 1;
        });
    }
</script>
@endpush
