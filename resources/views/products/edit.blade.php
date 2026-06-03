@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')
@section('page-icon', 'bi-pencil-square')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="section-title mb-0">
                    <i class="bi bi-pencil-square"></i>
                    Edit: {{ $product->name }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $product->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" id="sku" name="sku"
                                class="form-control @error('sku') is-invalid @enderror"
                                value="{{ old('sku', $product->sku) }}" required>
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="category_id" class="form-label">Category</label>
                            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">— No Category —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select id="supplier_id" name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                                <option value="">— No Supplier —</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>
                                        {{ $sup->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="discontinued" {{ old('status', $product->status) == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="price" name="price" step="0.01" min="0"
                                    class="form-control @error('price') is-invalid @enderror"
                                    value="{{ old('price', $product->price) }}" required>
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Current Quantity</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" id="quantity" name="quantity" min="0"
                                    class="form-control @error('quantity') is-invalid @enderror"
                                    value="{{ old('quantity', $product->quantity) }}" readonly>
                                <span class="badge bg-info-custom" style="white-space:nowrap;">
                                    <i class="bi bi-info-circle me-1"></i>Use Stock In/Out
                                </span>
                            </div>
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="reorder_level" class="form-label">Reorder Level</label>
                            <input type="number" id="reorder_level" name="reorder_level" min="0"
                                class="form-control @error('reorder_level') is-invalid @enderror"
                                value="{{ old('reorder_level', $product->reorder_level) }}">
                            @error('reorder_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="warehouse_location" class="form-label">Warehouse Location</label>
                            <input type="text" id="warehouse_location" name="warehouse_location"
                                class="form-control @error('warehouse_location') is-invalid @enderror"
                                value="{{ old('warehouse_location', $product->warehouse_location) }}" placeholder="e.g. Aisle 3, Shelf B">
                            @error('warehouse_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="image" class="form-label">Product Image</label>
                            @if($product->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                        style="width:80px;height:80px;border-radius:10px;object-fit:cover;border:2px solid #e2e8f0;">
                                </div>
                            @endif
                            <input type="file" id="image" name="image"
                                class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <div class="form-text">JPEG, PNG, GIF. Max 2MB. Leave empty to keep current image.</div>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Update Product
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
