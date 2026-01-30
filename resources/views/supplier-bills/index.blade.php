@extends('layouts.wms')

@section('title', 'Tagihan Supplier')

@section('content')
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stat-card warning">
                <i class="bi bi-clock stat-icon"></i>
                <div class="stat-value">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
                <div class="stat-label">Total Belum Lunas</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card success">
                <i class="bi bi-check-circle stat-icon"></i>
                <div class="stat-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                <div class="stat-label">Total Sudah Lunas</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-receipt me-2"></i>Daftar Tagihan Supplier</span>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateModal">
                    <i class="bi bi-calculator me-1"></i>Generate Tagihan
                </button>
                <a href="{{ route('supplier-bills.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Manual
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter -->
            <form method="GET" class="row g-3 mb-4">
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
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <select name="year" class="form-select">
                        <option value="">Semua</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua</option>
                        <option value="belum_lunas" {{ request('status') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    @if(request()->hasAny(['supplier_id', 'year', 'status']))
                        <a href="{{ route('supplier-bills.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Periode</th>
                            <th>Supplier</th>
                            <th>Total Tagihan</th>
                            <th>Status</th>
                            <th>Tgl Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $index => $bill)
                            <tr>
                                <td>{{ $bills->firstItem() + $index }}</td>
                                <td>{{ $bill->period_label }}</td>
                                <td>{{ $bill->supplier->name }}</td>
                                <td><strong>Rp {{ number_format($bill->total_amount, 0, ',', '.') }}</strong></td>
                                <td>
                                    @if($bill->status == 'lunas')
                                        <span class="badge badge-success">Lunas</span>
                                    @else
                                        <span class="badge badge-warning">Belum Lunas</span>
                                    @endif
                                </td>
                                <td>{{ $bill->paid_at ? $bill->paid_at->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('supplier-bills.show', $bill) }}" class="btn btn-outline-primary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($bill->status == 'belum_lunas')
                                            <form action="{{ route('supplier-bills.mark-paid', $bill) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai tagihan ini sebagai lunas?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-success" title="Tandai Lunas">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('supplier-bills.edit', $bill) }}" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('supplier-bills.destroy', $bill) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus tagihan ini?')">
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
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data tagihan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $bills->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <!-- Generate Modal -->
    <div class="modal fade" id="generateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calculator me-2"></i>Generate Tagihan dari Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('supplier-bills.generate') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted">Generate tagihan otomatis dari data barang masuk</p>
                        
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bulan</label>
                                <select name="period_month" class="form-select" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun</label>
                                <select name="period_year" class="form-select" required>
                                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
