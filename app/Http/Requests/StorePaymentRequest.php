<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePaymentRequest extends FormRequest
{
    private const ONLINE_MINIMUM = 1;

    private const ONLINE_MAXIMUM = 100_000;

    public function authorize(): bool
    {
        return $this->user()?->role === 'client';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'loan_id' => ['required', 'integer', Rule::exists('loans', 'id')],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:100000000'],
            'method' => ['required', Rule::in(['gcash', 'paymaya', 'bank_transfer', 'cash'])],
            'proof' => ['required_if:method,cash', 'nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->input('method') === 'cash' || ! is_numeric($this->input('amount'))) {
                    return;
                }

                $amount = (float) $this->input('amount');

                if ($amount > self::ONLINE_MAXIMUM) {
                    $validator->errors()->add('amount', 'Online payments cannot exceed PHP 100,000 per transaction.');
                }

                if ($amount < self::ONLINE_MINIMUM) {
                    $validator->errors()->add('amount', 'Online payments must be at least PHP 1.00.');
                }
            },
        ];
    }
}
