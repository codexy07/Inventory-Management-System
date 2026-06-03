@extends('layouts.admin')

@section('title', 'Sales Report')
@section('page-title', 'Sales Report')
@section('page-icon', 'bi-currency-dollar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Sales Report</h5>
        <small class="text-muted">Revenue and order analytics</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.export.csv', ['type' => 'sales']) }}" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Export CSV
        </a>
        <a href="{{ route('reports.export.pdf', ['type' => 'sales']) }}" class="btn btn-outline-danger" target="_blank">
            <i class="bi bi-file-pdf me-1"></i> Export PDF
        </a>
    </div>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-products p-3 text-white h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value mt-1">${{ number_format($totalRevenue, 2) }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-stock p-3 text-white h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Completed Orders</div>
                    <div class="stat-value mt-1">{{ $totalOrders }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-cart-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-value-card p-3 text-white h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Avg Order Value</div>
                    <div class="stat-value mt-1">${{ number_format($avgOrderValue, 2) }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-graph-up"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 fade-in-up">
        <div class="stat-card stat-lowstock p-3 text-white h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">This Month</div>
                    <div class="stat-value mt-1">${{ number_format($monthlySales->first()->revenue ?? 0, 2) }}</div>
                </div>
                <div class="stat-icon"><i class="bi bi-calendar-month"></i></div>
            </div>
        </div>
    </div>
</div>

{{-- Monthly Summary --}}
@if($monthlySales->count() > 0)
<div class="card mb-4">
    <div class="card-header">
        <h5 class="section-title mb-0">
            <i class="bi bi-bar-chart"></i> Monthly Revenue
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Orders</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlySales as $month)
                        <tr>
                            <td class="fw-semibold">{{ date('F Y', strtotime($month->year . '-' . str_pad($month->month, 2, '0', STR_PAD_LEFT) . '-01')) }}</td>
                            <td><span class="badge bg-info-custom">{{ $month->order_count }}</span></td>
                            <td class="fw-bold" style="color:var(--accent);">${{ number_format($month->revenue, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Orders Table --}}
<div class="card">
    <div class="card-header">
        <h5 class="section-title mb-0">
            <i class="bi bi-receipt"></i> Completed Orders
        </h5>
    </div>
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
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td><span class="fw-bold" style="color:var(--accent);">#{{ $order->id }}</span></td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->product->name ?? 'N/A' }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td class="fw-bold">${{ number_format($order->total_price, 2) }}</td>
                                <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-inbox empty-icon"></i>
                <p class="empty-text">No completed orders yet.</p>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">{{ $orders->links() }}</div>
@endsection
