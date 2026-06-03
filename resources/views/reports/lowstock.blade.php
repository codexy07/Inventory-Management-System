@extends('layouts.admin')

@section('title', 'Low Stock Report')
@section('page-title', 'Low Stock Report')
@section('page-icon', 'bi-exclamation-triangle')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="section-title mb-0">
            <i class="bi bi-exclamation-triangle" style="color:#d97706;"></i>
            Low Stock Products
            @if($products->total() > 0)
                <span class="badge bg-warning-custom ms-2">{{ $products->total() }}</span>
            @endif
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Current Quantity</th>
                        <th>Reorder Level</th>
                        <th>Deficit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $i => $product)
                        <tr>
                            <td class="text-muted">{{ $products->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>
                                <span class="badge {{ $product->quantity <= 0 ? 'bg-danger-custom' : 'bg-warning-custom' }} fs-6 rounded-pill">
                                    {{ $product->quantity }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $product->reorder_level }}</td>
                            <td class="text-danger fw-bold">
                                {{ max(0, $product->reorder_level - $product->quantity) }}
                            </td>
                            <td>
                                @if($product->quantity <= 0)
                                    <span class="badge bg-danger-custom">
                                        <i class="bi bi-x-circle me-1"></i>Out of Stock
                                    </span>
                                @else
                                    <span class="badge bg-warning-custom">
                                        <i class="bi bi-exclamation me-1"></i>Reorder Soon
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-check-circle empty-icon" style="color:#059669;"></i>
                                    <p class="empty-text" style="color:#059669;">All products are well-stocked!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
