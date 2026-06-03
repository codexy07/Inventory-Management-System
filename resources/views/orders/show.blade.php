@extends('layouts.admin')

@section('title', 'Order #' . $order->id)
@section('page-title', 'Order Details')
@section('page-icon', 'bi-receipt')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Order #{{ $order->id }}</h5>
        <small class="text-muted">Placed on {{ $order->created_at->format('M d, Y h:i A') }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('orders.invoice', $order) }}" class="btn btn-outline-primary" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> Invoice
        </a>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="section-title mb-0">
                    <i class="bi bi-cart-check"></i> Order Information
                </h5>
                <span class="badge {{ $order->status_badge }} fs-6">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Product</label>
                        <div class="fw-semibold fs-5">{{ $order->product->name ?? 'N/A' }}</div>
                        <small class="text-muted">SKU: {{ $order->product->sku ?? 'N/A' }}</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small">Quantity</label>
                        <div class="fw-semibold fs-5">{{ $order->quantity }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small">Unit Price</label>
                        <div class="fw-semibold fs-5">${{ number_format($order->unit_price, 2) }}</div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Customer</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar" style="width:28px;height:28px;font-size:0.65rem;border-radius:8px;">
                                {{ strtoupper(substr($order->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $order->user->name }}</div>
                                <small class="text-muted">{{ $order->user->email }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Order Date</label>
                        <div class="fw-semibold">{{ $order->created_at->format('M d, Y') }}</div>
                        <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">Total Amount</label>
                        <div class="fw-bold fs-4" style="color:var(--accent);">${{ number_format($order->total_price, 2) }}</div>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-4 p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
                        <label class="form-label text-muted small mb-1">Notes</label>
                        <div class="text-body">{{ $order->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-calculator"></i> Order Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Unit Price:</span>
                    <span class="fw-semibold">${{ number_format($order->unit_price, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Quantity:</span>
                    <span class="fw-semibold">{{ $order->quantity }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold fs-5">Total:</span>
                    <span class="fw-bold fs-5" style="color:var(--accent);">${{ number_format($order->total_price, 2) }}</span>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-gear"></i> Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary w-100 mb-2">
                    <i class="bi bi-pencil me-1"></i> Edit Order
                </a>
                <a href="{{ route('orders.invoice', $order) }}" class="btn btn-outline-primary w-100 mb-2" target="_blank">
                    <i class="bi bi-file-pdf me-1"></i> Download Invoice
                </a>
                <button class="btn btn-outline-danger w-100"
                    onclick="confirmDelete('{{ route('orders.destroy', $order) }}', 'Are you sure you want to delete Order #{{ $order->id }}?')">
                    <i class="bi bi-trash me-1"></i> Delete Order
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
