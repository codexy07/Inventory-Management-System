<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $supplierId = $this->route('supplier')?->id ?? $this->route('supplier');

        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:suppliers,phone,' . $supplierId,
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $supplierId,
            'address' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Supplier name is required.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
        ];
    }
}
