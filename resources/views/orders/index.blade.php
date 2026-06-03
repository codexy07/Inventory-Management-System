@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders')
@section('page-icon', 'bi-cart')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Order Management</h5>
        <small class="text-muted">Manage customer orders and track status</small>
    </div>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Order
    </a>
</div>

{{-- Filter Bar --}}
<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Search by product or customer..." value="{{ request('search') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-search me-1"></i> Filter
            </button>
        </div>
    </form>
</div>

{{-- Orders Table --}}
<div class="card">
    <div class="card-body p-0">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <span class="fw-bold" style="color:var(--accent);">#{{ $order->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar" style="width:28px;height:28px;font-size:0.65rem;border-radius:8px;">
                                            {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                        </div>
                                        <span class="fw-medium small">{{ $order->user->name }}</span>
                                    </div>
                                </td>
                                <td class="fw-medium">{{ $order->product->name ?? 'N/A' }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td class="fw-bold">${{ number_format($order->total_price, 2) }}</td>
                                <td>
                                    <span class="badge {{ $order->status_badge }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger" title="Delete"
                                                onclick="confirmDelete('{{ route('orders.destroy', $order) }}', 'Are you sure you want to delete Order #{{ $order->id }}? This will restore the stock.')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-cart empty-icon"></i>
                <p class="empty-text">No orders found.</p>
                <a href="{{ route('orders.create') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-lg me-1"></i> Create First Order
                </a>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    {{ $orders->withQueryString()->links() }}
</div>
@endsection
