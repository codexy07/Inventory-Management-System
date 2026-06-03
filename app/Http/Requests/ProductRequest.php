<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'name' => 'required|string|max:255|unique:products,name,' . $productId,
            'sku' => 'required|string|max:50|unique:products,sku,' . $productId,
            'category_id' => 'nullable|integer|exists:categories,id',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'warehouse_location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,discontinued',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Product name is required.',
            'name.unique' => 'This product name already exists.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU already exists.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price cannot be negative.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity cannot be negative.',
            'status.required' => 'Status is required.',
        ];
    }
}
