@extends('layouts.admin')

@section('title', 'Edit Stock Out')
@section('page-title', 'Edit Stock Out')
@section('page-icon', 'bi-pencil-square')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-pencil-square" style="color:#dc2626;"></i>
                    Edit Stock-Out Record
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <span>Changing the quantity will automatically adjust the product's current stock level.</span>
                </div>

                <form method="POST" action="{{ route('stock.out.update', $stockOut) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                            <select id="product_id" name="product_id"
                                class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">— Select Product —</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $stockOut->product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->quantity }} available)
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" id="quantity" name="quantity" min="1"
                                class="form-control @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity', $stockOut->quantity) }}" required>
                            <div class="form-text">
                                Previously: <strong>{{ $stockOut->quantity }}</strong> units
                            </div>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="text" id="date" name="date"
                                class="form-control datepicker @error('date') is-invalid @enderror"
                                value="{{ old('date', \Carbon\Carbon::parse($stockOut->date)->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Update Record
                        </button>
                        <a href="{{ route('stock.out.index') }}" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
