@extends('layouts.admin')

@section('title', 'Suppliers')
@section('page-title', 'Suppliers')
@section('page-icon', 'bi-truck')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Supplier Management</h5>
        <small class="text-muted">{{ $suppliers->total() }} total suppliers</small>
    </div>
    @can('create')
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Supplier
    </a>
    @endcan
</div>

{{-- Filter Bar --}}
<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-10">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, or phone..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search me-1"></i> Search
            </button>
        </div>
    </form>
</div>

{{-- Suppliers Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($suppliers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Products</th>
                            @can('edit')<th style="width:100px;">Actions</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $i => $supplier)
                            <tr>
                                <td class="text-muted">{{ $suppliers->firstItem() + $i }}</td>
                                <td class="fw-semibold">{{ $supplier->name }}</td>
                                <td>
                                    <span class="d-flex align-items-center gap-1">
                                        <i class="bi bi-telephone text-muted small"></i>
                                        {{ $supplier->phone }}
                                    </span>
                                </td>
                                <td>
                                    @if($supplier->email)
                                        <span class="d-flex align-items-center gap-1">
                                            <i class="bi bi-envelope text-muted small"></i>
                                            {{ $supplier->email }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted" style="max-width:200px;">{{ $supplier->address ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-info-custom rounded-pill">{{ $supplier->products_count }}</span>
                                </td>
                                @can('edit')
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ route('suppliers.destroy', $supplier) }}')"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-truck empty-icon"></i>
                                        <p class="empty-text">No suppliers found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-truck empty-icon"></i>
                <p class="empty-text">No suppliers found.</p>
                @can('create')
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm mt-2">
                        <i class="bi bi-plus-lg me-1"></i> Add Supplier
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $suppliers->withQueryString()->links() }}
</div>
@endsection
