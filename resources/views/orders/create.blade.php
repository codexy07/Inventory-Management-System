@extends('layouts.admin')

@section('title', 'Create Order')
@section('page-title', 'Create New Order')
@section('page-icon', 'bi-cart-plus')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">New Order</h5>
        <small class="text-muted">Create a new customer order</small>
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
                    <i class="bi bi-cart-check"></i> Order Details
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">Select a product...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}
                                    data-price="{{ $product->price }}" data-stock="{{ $product->quantity }}">
                                    {{ $product->name }} (SKU: {{ $product->sku }}) — ${{ number_format($product->price, 2) }} | Stock: {{ $product->quantity }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity', 1) }}" min="1" required>
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit Price</label>
                            <input type="text" id="unit_price_display" class="form-control" readonly value="$0.00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Optional order notes...">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div>
                            <span class="text-muted">Estimated Total:</span>
                            <span class="fw-bold fs-5 ms-2" id="total_display" style="color:var(--accent);">$0.00</span>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Place Order
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
                    <i class="bi bi-info-circle"></i> Order Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-3">
                    <i class="bi bi-receipt fs-1" style="color:var(--accent);"></i>
                    <p class="text-muted mt-2 mb-0" id="summary_text">Select a product to see order details</p>
                </div>
                <div id="order_summary" class="d-none">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Product:</span>
                        <span class="fw-semibold" id="summary_product">—</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Available Stock:</span>
                        <span class="fw-semibold" id="summary_stock">—</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Unit Price:</span>
                        <span class="fw-semibold" id="summary_price">—</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Quantity:</span>
                        <span class="fw-semibold" id="summary_qty">—</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total:</span>
                        <span class="fw-bold fs-5" style="color:var(--accent);" id="summary_total">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const unitPriceDisplay = document.getElementById('unit_price_display');
    const totalDisplay = document.getElementById('total_display');
    const summaryText = document.getElementById('summary_text');
    const orderSummary = document.getElementById('order_summary');

    function updateSummary() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const price = parseFloat(selectedOption.dataset.price || 0);
        const stock = parseInt(selectedOption.dataset.stock || 0);
        const qty = parseInt(quantityInput.value || 0);
        const total = price * qty;

        if (productSelect.value) {
            summaryText.classList.add('d-none');
            orderSummary.classList.remove('d-none');
            document.getElementById('summary_product').textContent = selectedOption.text.split(' (')[0];
            document.getElementById('summary_stock').textContent = stock + ' units';
            document.getElementById('summary_price').textContent = '$' + price.toFixed(2);
            document.getElementById('summary_qty').textContent = qty;
            document.getElementById('summary_total').textContent = '$' + total.toFixed(2);
        } else {
            summaryText.classList.remove('d-none');
            orderSummary.classList.add('d-none');
        }

        unitPriceDisplay.value = '$' + price.toFixed(2);
        totalDisplay.textContent = '$' + total.toFixed(2);
    }

    productSelect.addEventListener('change', updateSummary);
    quantityInput.addEventListener('input', updateSummary);
    updateSummary();
});
</script>
@endpush
@endsection
