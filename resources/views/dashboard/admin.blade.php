@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-icon', 'bi-shield-check')
@section('page-title', 'Admin Dashboard')

@section('content')
{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-gradient-purple" style="animation-delay:0.05s;">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="stat-label">Total Products</div>
            <div class="stat-value">{{ $totalProducts }}</div>
            <div class="stat-trend text-white-50">All inventory items</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-gradient-blue" style="animation-delay:0.10s;">
            <div class="stat-icon"><i class="bi bi-cart"></i></div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ $totalOrders }}</div>
            <div class="stat-trend text-white-50">{{ $pendingOrders }} pending</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-gradient-amber" style="animation-delay:0.15s;">
            <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
            <div class="stat-label">Revenue</div>
            <div class="stat-value">${{ number_format($totalRevenue, 0) }}</div>
            <div class="stat-trend text-white-50">{{ $completedOrders }} completed</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card stat-gradient-emerald" style="animation-delay:0.20s;">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-label">Low Stock Items</div>
            <div class="stat-value">{{ $lowStockProducts }}</div>
            <div class="stat-trend {{ $lowStockProducts > 0 ? 'text-warning' : 'text-white-50' }}">
                {{ $lowStockProducts > 0 ? 'Needs attention!' : 'All stocked up' }}
            </div>
        </div>
    </div>
</div>

{{-- Second Row Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-products p-3 text-white h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Stock</div>
                    <div class="stat-value mt-1">{{ number_format($totalQuantity) }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-boxes"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-stock p-3 text-white h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Suppliers</div>
                    <div class="stat-value mt-1">{{ $totalSuppliers }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-truck"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-value-card p-3 text-white h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Categories</div>
                    <div class="stat-value mt-1">{{ $totalCategories }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-tags"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-lowstock p-3 text-white h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Users</div>
                    <div class="stat-value mt-1">{{ $totalUsers }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-people"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Quick Management Links --}}
    <div class="col-lg-8">
        <div class="card card-premium h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-grid-3x3-gap-fill" style="color:#a78bfa;"></i>
                <span class="fw-semibold">Quick Management</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-4">
                        <a href="{{ route('products.index') }}" class="quick-action-card">
                            <div class="qa-icon" style="background:rgba(124,58,237,0.12);color:#a78bfa;"><i class="bi bi-box"></i></div>
                            <div class="qa-label">Products</div>
                            <div class="qa-count">{{ $totalProducts }} items</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('orders.index') }}" class="quick-action-card">
                            <div class="qa-icon" style="background:rgba(59,130,246,0.12);color:#93c5fd;"><i class="bi bi-cart"></i></div>
                            <div class="qa-label">Orders</div>
                            <div class="qa-count">{{ $totalOrders }} total</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('suppliers.index') }}" class="quick-action-card">
                            <div class="qa-icon" style="background:rgba(236,72,153,0.12);color:#f9a8d4;"><i class="bi bi-truck"></i></div>
                            <div class="qa-label">Suppliers</div>
                            <div class="qa-count">{{ $totalSuppliers }} partners</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('categories.index') }}" class="quick-action-card">
                            <div class="qa-icon" style="background:rgba(245,158,11,0.12);color:#fcd34d;"><i class="bi bi-tags"></i></div>
                            <div class="qa-label">Categories</div>
                            <div class="qa-count">{{ $totalCategories }} groups</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('stock.in.create') }}" class="quick-action-card">
                            <div class="qa-icon" style="background:rgba(16,185,129,0.12);color:#6ee7b7;"><i class="bi bi-arrow-down-circle"></i></div>
                            <div class="qa-label">Stock In</div>
                            <div class="qa-count">Add inventory</div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4">
                        <a href="{{ route('users.index') }}" class="quick-action-card">
                            <div class="qa-icon" style="background:rgba(139,92,246,0.12);color:#c4b5fd;"><i class="bi bi-people"></i></div>
                            <div class="qa-label">Users</div>
                            <div class="qa-count">{{ $totalUsers }} accounts</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-lg-4">
        <div class="card card-premium h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-activity" style="color:#6ee7b7;"></i>
                <span class="fw-semibold">Recent Activity</span>
            </div>
            <div class="card-body p-0">
                @if(isset($recentTransactions) && count($recentTransactions) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentTransactions as $txn)
                            <div class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
                                <div class="txn-icon {{ $txn->type === 'in' ? 'txn-in' : 'txn-out' }}">
                                    <i class="bi {{ $txn->type === 'in' ? 'bi-arrow-down-circle' : 'bi-arrow-up-circle' }}"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold small text-truncate">{{ $txn->product->name ?? 'Unknown Product' }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $txn->type === 'in' ? 'Stock In' : 'Stock Out' }} · {{ $txn->quantity }} units</div>
                                </div>
                                <span class="small text-muted flex-shrink-0">{{ $txn->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">No recent transactions</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Recent Orders --}}
@if(isset($recentOrders) && count($recentOrders) > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card card-premium">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-cart-check" style="color:#93c5fd;"></i>
                    <span class="fw-semibold">Recent Orders</span>
                </div>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                                <tr>
                                    <td><span class="fw-bold" style="color:var(--accent);">#{{ $order->id }}</span></td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->product->name ?? 'N/A' }}</td>
                                    <td class="fw-bold">${{ number_format($order->total_price, 2) }}</td>
                                    <td><span class="badge {{ $order->status_badge }}">{{ ucfirst($order->status) }}</span></td>
                                    <td class="text-muted small">{{ $order->created_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Top Moving Products --}}
@if(isset($topMovingProducts) && count($topMovingProducts) > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card card-premium">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow" style="color:#fcd34d;"></i>
                <span class="fw-semibold">Top Moving Products</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Stock In</th>
                                <th>Stock Out</th>
                                <th>Total Movements</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topMovingProducts as $product)
                                <tr>
                                    <td class="fw-medium">{{ $product->name }}</td>
                                    <td>
                                        @if($product->category)
                                            <span class="badge badge-custom badge-amber">{{ $product->category->name }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-custom badge-green">+{{ $product->stock_in_count }}</span></td>
                                    <td><span class="badge badge-custom badge-red">-{{ $product->stock_out_count }}</span></td>
                                    <td><span class="fw-semibold">{{ $product->stock_in_count + $product->stock_out_count }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- System Overview --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card card-premium">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-info-circle" style="color:#93c5fd;"></i>
                <span class="fw-semibold">System Overview</span>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="display-6 fw-bold" style="color:#a78bfa;">{{ $totalUsers ?? 0 }}</div>
                            <div class="text-muted small">Registered Users</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="display-6 fw-bold" style="color:#93c5fd;">{{ $totalCategories ?? 0 }}</div>
                            <div class="text-muted small">Categories</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="display-6 fw-bold" style="color:#6ee7b7;">{{ $totalSuppliers ?? 0 }}</div>
                            <div class="text-muted small">Suppliers</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-center">
                            <div class="display-6 fw-bold" style="color:#fcd34d;">${{ number_format($totalRevenue, 0) }}</div>
                            <div class="text-muted small">Total Revenue</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
