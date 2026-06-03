<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockOutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'Selected product does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a whole number.',
            'quantity.min' => 'Quantity must be at least 1.',
            'date.required' => 'Date is required.',
            'date.date_format' => 'Date must be in YYYY-MM-DD format.',
        ];
    }
}
