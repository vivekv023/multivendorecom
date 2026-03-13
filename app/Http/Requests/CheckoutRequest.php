<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address' => 'required|string|min:10|max:500',
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'nullable|string|in:cash_on_delivery,credit_card,debit_card,paypal,bank_transfer',
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.required' => 'Shipping address is required',
            'shipping_address.min' => 'Shipping address must be at least 10 characters',
            'notes.max' => 'Notes cannot exceed 500 characters',
            'payment_method.in' => 'Invalid payment method selected',
        ];
    }
}
