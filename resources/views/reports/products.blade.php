@extends('layouts.admin')

@section('title', 'Product Report')
@section('page-title', 'Product Report')
@section('page-icon', 'bi-box')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Product Report</h5>
        <small class="text-muted">Detailed product inventory and movement data</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export.csv', ['type' => 'products']) }}" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export CSV
        </a>
        <a href="{{ route('reports.export.pdf', ['type' => 'products']) }}" class="btn btn-outline-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> Export PDF
        </a>
    </div>
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
                            <th>Current Qty</th>
                            <th>Stock In</th>
                            <th>Stock Out</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $i => $product)
                            <tr>
                                <td class="text-muted">{{ $products->firstItem() + $i }}</td>
                                <td class="fw-semibold">{{ $product->name }}</td>
                                <td><code style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-size:0.75rem;">{{ $product->sku ?? '—' }}</code></td>
                                <td>
                                    @if($product->category)
                                        <span class="badge bg-amber-custom rounded-pill">{{ $product->category->name }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $product->supplier->name ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $product->quantity <= $product->reorder_level ? 'bg-warning-custom' : 'bg-success-custom' }} rounded-pill">
                                        {{ $product->quantity }}
                                    </span>
                                </td>
                                <td><span class="text-success fw-semibold">+{{ $product->total_stock_in }}</span></td>
                                <td><span class="text-danger fw-semibold">-{{ $product->total_stock_out }}</span></td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td><span class="badge {{ $product->status_badge }}">{{ ucfirst($product->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-inbox empty-icon"></i>
                <p class="empty-text">No products found.</p>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">{{ $products->links() }}</div>
@endsection
