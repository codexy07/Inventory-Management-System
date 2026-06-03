@extends('layouts.admin')

@section('title', 'Inventory Report')
@section('page-title', 'Inventory Report')
@section('page-icon', 'bi-file-earmark-text')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="section-title mb-0">
            <i class="bi bi-file-earmark-text"></i>
            Inventory Summary
            <span class="text-muted fw-normal small ms-2">({{ $products->total() }} products)</span>
        </h5>
        @can('export-reports')
        <form action="{{ route('reports.export.pdf') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-filetype-pdf me-1"></i> Export PDF
            </button>
        </form>
        @endcan
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $i => $product)
                        <tr>
                            <td class="text-muted">{{ $products->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $product->name }}</td>
                            <td>{{ $product->quantity }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td class="fw-semibold">${{ number_format($product->total_value, 2) }}</td>
                            <td>
                                @if($product->quantity <= 0)
                                    <span class="badge bg-danger-custom"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>
                                @elseif($product->quantity <= $product->reorder_level)
                                    <span class="badge bg-warning-custom"><i class="bi bi-exclamation me-1"></i>Low Stock</span>
                                @else
                                    <span class="badge bg-success-custom"><i class="bi bi-check-circle me-1"></i>In Stock</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox empty-icon"></i>
                                    <p class="empty-text">No products found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background:#f8fafc;">
                        <td colspan="4" class="text-end fw-bold">Grand Total:</td>
                        <td class="fw-bold fs-5" style="color:var(--accent);">${{ number_format($grandTotal, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
