<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // role and venue checks are done by the route middleware and controller
    }

    public function rules(): array
    {
        $venue = $this->route('venue');

        return [
            'start_date'  => ['required', 'date', 'after_or_equal:today'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:'.$venue->capacity],
            // A receipt is required only when the venue asks for a down payment.
            'receipt'     => [$venue->down_payment > 0 ? 'required' : 'nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        $venue = $this->route('venue');

        return [
            'guest_count.max'        => 'This venue holds up to '.$venue->capacity.' guests.',
            'end_date.after_or_equal' => 'The end date cannot be before the start date.',
            'receipt.required'       => 'Please upload your GCash receipt for the down payment.',
        ];
    }
}