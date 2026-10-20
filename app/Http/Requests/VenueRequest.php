<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VenueRequest extends FormRequest
{
    // Roles and ownership are checked by the route middleware and the policy.
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barangay_id'  => ['required', 'exists:barangays,id'],
            'name'         => ['required', 'string', 'max:255'],
            'address'      => ['nullable', 'string', 'max:255'],
            'capacity'     => ['required', 'integer', 'min:1', 'max:5000'],
            'base_price'   => ['required', 'numeric', 'min:0'],
            'down_payment' => ['required', 'numeric', 'min:0', 'lte:base_price'],
            'gcash_number' => ['nullable', 'regex:/^09\d{9}$/'],
            'amenities'    => ['nullable', 'array'],
            'amenities.*'  => ['exists:amenities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'down_payment.lte' => 'The down payment cannot be more than the base price.',
            'gcash_number.regex' => 'Enter an 11-digit mobile number starting with 09.',
        ];
    }
}