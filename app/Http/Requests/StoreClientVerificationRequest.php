<?php

namespace App\Http\Requests;

use App\Rules\DigitalSignature;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreClientVerificationRequest extends FormRequest
{
    private const MAX_COMBINED_UPLOAD_BYTES = 3_750_000;

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
            'profile_photo' => [$this->user()?->profile_photo_path ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
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

    protected function prepareForValidation(): void
    {
        $this->merge(collect([
            'company_name',
            'job_title',
            'source_of_income',
            'valid_id_type',
            'valid_id_number',
            'additional_information',
        ])->mapWithKeys(function (string $field): array {
            $value = $this->input($field);

            return [$field => is_string($value) ? trim($value) : $value];
        })->all());
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $totalBytes = collect(['profile_photo', 'valid_id', 'selfie_with_id', 'payslip'])
                ->sum(fn (string $field): int => (int) ($this->file($field)?->getSize() ?? 0));

            if ($totalBytes > self::MAX_COMBINED_UPLOAD_BYTES) {
                $validator->errors()->add(
                    'valid_id',
                    'The selected files are too large together. Keep the combined upload under 3.6 MB.',
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'profile_photo.required' => 'Upload a clear profile photo before submitting your verification.',
            'company_name.required_if' => 'Enter your employer or business name for the selected employment status.',
            'job_title.required_if' => 'Enter your job title or occupation for the selected employment status.',
            'valid_id.required' => 'Upload a clear copy of your valid ID.',
            'selfie_with_id.required' => 'Upload a selfie while holding your valid ID.',
            'digital_signature.required' => 'Draw your digital signature before submitting.',
        ];
    }

    protected function failedAuthorization(): void
    {
        abort(403);
    }
}
