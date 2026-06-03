@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')
@section('page-icon', 'bi-box')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Product Inventory</h5>
        <small class="text-muted">{{ $products->total() }} total products</small>
    </div>
    @can('create')
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Product
    </a>
    @endcan
</div>

{{-- Filter Bar --}}
<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search by name or SKU..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search me-1"></i> Filter
            </button>
        </div>
    </form>
</div>

{{-- Products Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Location</th>
                            @can('edit')<th style="width:100px;">Actions</th>@endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $i => $product)
                            <tr>
                                <td class="text-muted">{{ $products->firstItem() + $i }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                style="width:36px;height:36px;border-radius:8px;object-fit:cover;">
                                        @else
                                            <div class="category-badge">
                                                <i class="bi bi-box"></i>
                                            </div>
                                        @endif
                                        <span class="fw-semibold">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td><code style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-size:0.75rem;">{{ $product->sku }}</code></td>
                                <td>
                                    @if($product->category)
                                        <span class="badge bg-amber-custom rounded-pill">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $product->supplier->name ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $product->quantity <= $product->reorder_level ? 'bg-warning-custom' : 'bg-success-custom' }} rounded-pill">
                                        {{ $product->quantity }}
                                    </span>
                                </td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td><span class="badge {{ $product->status_badge }}">{{ ucfirst($product->status) }}</span></td>
                                <td class="small text-muted">{{ $product->warehouse_location ?? '—' }}</td>
                                @can('edit')
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ route('products.destroy', $product) }}')"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox empty-icon"></i>
                                        <p class="empty-text">No products found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-inbox empty-icon"></i>
                <p class="empty-text">No products found.</p>
                @can('create')
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm mt-2">
                        <i class="bi bi-plus-lg me-1"></i> Add Product
                    </a>
                @endcan
            </div>
        @endif
    </div>
</div>

<div class="mt-3 d-flex justify-content-center">
    {{ $products->withQueryString()->links() }}
</div>
@endsection
