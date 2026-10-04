<?php

namespace App\Http\Requests;

use App\Rules\DigitalSignature;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientVerificationRequest extends FormRequest
{
    protected $errorBag = 'verification';

    public function authorize(): bool
    {
        return $this->user()?->role === 'client';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $verification = $this->user()?->clientVerification;

        return [
            'employment_status' => ['required', Rule::in(['employed', 'self_employed', 'unemployed', 'student', 'retired', 'other'])],
            'company_name' => ['nullable', 'required_if:employment_status,employed,self_employed', 'string', 'max:255'],
            'job_title' => ['nullable', 'required_if:employment_status,employed,self_employed', 'string', 'max:255'],
            'monthly_income' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'employment_length_months' => ['required', 'integer', 'min:0', 'max:1200'],
            'source_of_income' => ['required', 'string', 'max:255'],
            'valid_id_type' => ['required', 'string', 'max:100'],
            'valid_id_number' => ['required', 'string', 'max:255'],
            'valid_id' => [$verification?->valid_id_path ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'selfie_with_id' => [$verification?->selfie_with_id_path ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'payslip' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
            'digital_signature' => [$verification?->digital_signature ? 'nullable' : 'required', 'string', 'max:500000', new DigitalSignature],
            'additional_information' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function failedAuthorization(): void
    {
        abort(403);
    }
}
