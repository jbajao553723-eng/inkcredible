<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'client';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $purposeOptions = config('loan_purposes', []);
        $allowedPurposes = array_keys($purposeOptions[$this->string('loan_type')->toString()] ?? []);

        return [
            'loan_type' => ['required', Rule::in(array_keys($purposeOptions))],
            'amount' => ['required', 'numeric', 'min:1'],
            'government_id' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'purpose_choice' => ['required', Rule::in($allowedPurposes)],
            'purpose_other' => ['nullable', 'string', 'max:255', 'required_if:purpose_choice,other', 'regex:/\S/'],
            'loan_terms_accepted' => ['accepted'],
        ];
    }
}
