@extends('layouts.admin')

@section('title', 'Stock Out History')
@section('page-title', 'Stock Out')
@section('page-icon', 'bi-arrow-up-circle')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="section-title mb-0">
            <i class="bi bi-arrow-up-circle" style="color:#dc2626;"></i>
            Stock Out Transactions
            <span class="text-muted fw-normal small ms-2">({{ $transactions->total() }})</span>
        </h5>
        @can('create')
        <a href="{{ route('stock.out.create') }}" class="btn btn-danger">
            <i class="bi bi-plus-lg me-1"></i> Remove Stock
        </a>
        @endcan
    </div>
    <div class="card-body p-4">
        {{-- Search --}}
        <form method="GET" class="mb-3">
            <div class="d-flex gap-2">
                <div class="flex-grow-1">
                    <input type="text" name="search" class="form-control"
                        placeholder="Search by product name..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-danger px-3">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('stock.out.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Current Stock</th>
                        @can('admin')<th style="width:100px;">Actions</th>@endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $i => $txn)
                        <tr>
                            <td class="text-muted">{{ $transactions->firstItem() + $i }}</td>
                            <td>
                                <span class="fw-semibold">{{ $txn->date->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <a href="{{ route('products.edit', $txn->product) }}" class="text-decoration-none fw-semibold">
                                    {{ $txn->product->name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-danger rounded-pill fs-6 px-3 py-1">
                                    <i class="bi bi-dash-circle me-1" style="font-size:0.7rem;"></i>
                                    -{{ $txn->quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $txn->product->quantity <= $txn->product->reorder_level ? 'bg-warning-custom' : 'bg-success-custom' }} rounded-pill">
                                    {{ $txn->product->quantity }}
                                </span>
                            </td>
                            @can('admin')
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('stock.out.edit', $txn) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button onclick="confirmDelete('{{ route('stock.out.destroy', $txn) }}',
                                        'Delete this stock-out record? The product quantity will be restored by {{ $txn->quantity }} units.')"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-inbox empty-icon"></i>
                                    <p class="empty-text">No stock-out transactions found.</p>
                                    @can('create')
                                        <a href="{{ route('stock.out.create') }}" class="btn btn-danger btn-sm mt-2">
                                            <i class="bi bi-plus-lg me-1"></i> Remove Stock
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex justify-content-center">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
