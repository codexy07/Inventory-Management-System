@extends('layouts.admin')

@section('title', 'Stock History')
@section('page-title', 'Stock History')
@section('page-icon', 'bi-clock-history')

@section('content')
{{-- Filter Card --}}
<div class="card mb-3">
    <div class="card-header">
        <h5 class="section-title mb-0">
            <i class="bi bi-funnel"></i>
            Filter Transactions
        </h5>
    </div>
    <div class="card-body p-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="product_id" class="form-label">Product</label>
                <select id="product_id" name="product_id" class="form-select">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">From Date</label>
                <input type="text" id="date_from" name="date_from"
                    class="form-control datepicker" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">To Date</label>
                <input type="text" id="date_to" name="date_to"
                    class="form-control datepicker" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request('product_id') || request('date_from') || request('date_to'))
                    <a href="{{ route('reports.history') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Transactions Card --}}
<div class="card">
    <div class="card-header">
        <h5 class="section-title mb-0">
            <i class="bi bi-list-ul"></i>
            Transaction History
        </h5>
    </div>
    <div class="card-body p-4">
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
                    @forelse($transactions as $transaction)
                        <tr>
                            <td class="text-muted small">{{ $transaction->date }}</td>
                            <td>
                                @if($transaction->type === 'Stock In')
                                    <span class="badge bg-success-custom">
                                        <i class="bi bi-arrow-down me-1"></i>IN
                                    </span>
                                @else
                                    <span class="badge bg-danger-custom">
                                        <i class="bi bi-arrow-up me-1"></i>OUT
                                    </span>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $transaction->product?->name ?? 'N/A' }}</td>
                            <td class="fw-bold">{{ $transaction->quantity }}</td>
                            <td class="text-muted">{{ $transaction->supplier_name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox empty-icon"></i>
                                    <p class="empty-text">No transactions found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
