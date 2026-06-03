@extends('layouts.admin')

@section('title', 'Edit Order #' . $order->id)
@section('page-title', 'Edit Order #' . $order->id)
@section('page-icon', 'bi-pencil')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Edit Order</h5>
        <small class="text-muted">Update order status and notes</small>
    </div>
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Orders
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-cart-check"></i> Order #{{ $order->id }} Details
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Product</label>
                        <div class="fw-semibold">{{ $order->product->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small">Quantity</label>
                        <div class="fw-semibold">{{ $order->quantity }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small">Unit Price</label>
                        <div class="fw-semibold">${{ number_format($order->unit_price, 2) }}</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Customer</label>
                        <div class="fw-semibold">{{ $order->user->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small">Created</label>
                        <div class="fw-semibold">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>

                <hr>

                <form action="{{ route('orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="form-text text-muted">Cancelling an order will restore the stock. Completing a cancelled order will decrement stock again.</small>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div class="fw-bold fs-5" style="color:var(--accent);">Total: ${{ number_format($order->total_price, 2) }}</div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Update Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-info-circle"></i> Status Legend
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-warning-custom">Pending</span>
                    <small class="text-muted">Order is awaiting processing</small>
                </div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-info-custom">Processing</span>
                    <small class="text-muted">Order is being processed</small>
                </div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-success-custom">Completed</span>
                    <small class="text-muted">Order has been fulfilled</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger-custom">Cancelled</span>
                    <small class="text-muted">Order was cancelled, stock restored</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
