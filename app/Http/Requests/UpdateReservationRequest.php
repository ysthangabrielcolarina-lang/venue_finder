<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    // The policy decides: only the customer who made it, and only while it is pending.
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('reservation'));
    }

    public function rules(): array
    {
        $reservation = $this->route('reservation');
        $venue = $reservation->venue;

        // A receipt is required only if the venue asks for a down payment and none was uploaded yet.
        $receiptRule = ($venue->down_payment > 0 && ! $reservation->receipt_path) ? 'required' : 'nullable';

        return [
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:'.$venue->capacity],
            'receipt' => [$receiptRule, 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        $venue = $this->route('reservation')->venue;

        return [
            'guest_count.max' => 'This venue holds up to '.$venue->capacity.' guests.',
            'end_date.after_or_equal' => 'The end date cannot be before the start date.',
            'receipt.required' => 'Please upload your GCash receipt for the down payment.',
        ];
    }
}
