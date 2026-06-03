@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-icon', 'bi-speedometer2')

@section('content')
{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-products p-3 text-white h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Products</div>
                    <div class="stat-value mt-1">{{ $totalProducts }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-box"></i></div>
            </div>
            <div class="mt-2 small" style="opacity:0.6;"><i class="bi bi-boxes me-1"></i> In inventory</div>
        </div>
    </div>
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-stock p-3 text-white h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Stock</div>
                    <div class="stat-value mt-1">{{ number_format($totalQuantity) }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-boxes"></i></div>
            </div>
            <div class="mt-2 small" style="opacity:0.6;"><i class="bi bi-box me-1"></i> Units in stock</div>
        </div>
    </div>
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-value-card p-3 text-white h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value mt-1">{{ $totalOrders ?? 0 }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-cart"></i></div>
            </div>
            <div class="mt-2 small" style="opacity:0.6;"><i class="bi bi-receipt me-1"></i> All orders</div>
        </div>
    </div>
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-lowstock p-3 text-white h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Low Stock Items</div>
                    <div class="stat-value mt-1">{{ $lowStockProducts->count() }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
            <div class="mt-2 small" style="opacity:0.6;"><i class="bi bi-bell me-1"></i> Needs attention</div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Low Stock Alerts --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-exclamation-triangle" style="color:#d97706;"></i>
                    Low Stock Alerts
                    @if($lowStockProducts->count() > 0)
                        <span class="badge bg-danger-custom ms-2">{{ $lowStockProducts->count() }}</span>
                    @endif
                </h5>
            </div>
            <div class="card-body p-4">
                @if($lowStockProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Reorder Level</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                    <tr>
                                        <td class="fw-semibold">{{ $product->name }}</td>
                                        <td>
                                            <span class="badge {{ $product->quantity <= 0 ? 'bg-danger-custom' : 'bg-warning-custom' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ $product->reorder_level }}</td>
                                        <td>
                                            @if($product->quantity <= 0)
                                                <span class="badge bg-danger-custom"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>
                                            @else
                                                <span class="badge bg-warning-custom"><i class="bi bi-exclamation me-1"></i>Low Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-check-circle empty-icon" style="color:#059669;"></i>
                        <p class="empty-text" style="color:#059669;">All products are well-stocked!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Moving Products --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-graph-up-arrow" style="color:#059669;"></i>
                    Top Moving Products
                    <span class="text-muted fw-normal small ms-2">(30 days)</span>
                </h5>
            </div>
            <div class="card-body p-4">
                @if($topMovingProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>Product</th>
                                    <th>Stock Out</th>
                                    <th>Current Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topMovingProducts as $i => $product)
                                    <tr>
                                        <td><span class="badge bg-secondary rounded-pill" style="background:#e2e8f0;color:#64748b;">{{ $i + 1 }}</span></td>
                                        <td class="fw-semibold">{{ $product->name }}</td>
                                        <td class="text-success fw-bold">{{ $product->total_out }}</td>
                                        <td>
                                            <span class="badge {{ $product->quantity <= $product->reorder_level ? 'bg-warning-custom' : 'bg-success-custom' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-inbox empty-icon"></i>
                        <p class="empty-text">No stock out transactions in last 30 days.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-clock-history" style="color:#3b82f6;"></i>
                    Recent Transactions
                </h5>
            </div>
            <div class="card-body p-4">
                @if($recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td class="text-muted small">{{ $transaction->date }}</td>
                                        <td>
                                            @if($transaction->type === 'Stock In')
                                                <span class="badge bg-success-custom"><i class="bi bi-arrow-down me-1"></i>IN</span>
                                            @else
                                                <span class="badge bg-danger-custom"><i class="bi bi-arrow-up me-1"></i>OUT</span>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">{{ $transaction->product?->name ?? 'N/A' }}</td>
                                        <td class="fw-bold">{{ $transaction->quantity }}</td>
                                        <td class="text-muted">{{ $transaction->supplier_name ?? ($transaction->supplier?->name ?? '-') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-inbox empty-icon"></i>
                        <p class="empty-text">No recent transactions found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
