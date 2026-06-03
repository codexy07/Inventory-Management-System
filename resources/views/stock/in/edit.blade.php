@extends('layouts.admin')

@section('title', 'Edit Stock In')
@section('page-title', 'Edit Stock In')
@section('page-icon', 'bi-pencil-square')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-pencil-square" style="color:#059669;"></i>
                    Edit Stock-In Record
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <span>Changing the quantity will automatically adjust the product's current stock level.</span>
                </div>

                <form method="POST" action="{{ route('stock.in.update', $stockIn) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                            <select id="product_id" name="product_id"
                                class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">— Select Product —</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id', $stockIn->product_id) == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->quantity }} in stock)
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select id="supplier_id" name="supplier_id"
                                class="form-select @error('supplier_id') is-invalid @enderror">
                                <option value="">— Select Supplier (Optional) —</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $stockIn->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" id="quantity" name="quantity" min="1"
                                class="form-control @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity', $stockIn->quantity) }}" required>
                            <div class="form-text">
                                Previously: <strong>{{ $stockIn->quantity }}</strong> units
                            </div>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="text" id="date" name="date"
                                class="form-control datepicker @error('date') is-invalid @enderror"
                                value="{{ old('date', \Carbon\Carbon::parse($stockIn->date)->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Update Record
                        </button>
                        <a href="{{ route('stock.in.index') }}" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
