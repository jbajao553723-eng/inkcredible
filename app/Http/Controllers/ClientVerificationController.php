<?php

namespace App\Http\Controllers;

use App\Models\ClientVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientVerificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === 'client', 403);

        $verification = $request->user()->clientVerification;

        if ($verification?->status === ClientVerification::STATUS_APPROVED) {
            return back()->with('error', 'Your account is already verified.');
        }

        if ($verification?->status === ClientVerification::STATUS_PENDING) {
            return back()->with('error', 'Your verification is already awaiting admin review.');
        }

        $validated = $request->validateWithBag('verification', [
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
            'additional_information' => ['nullable', 'string', 'max:2000'],
        ]);

        $documentDirectory = 'client-verifications/'.$request->user()->id;
        $validIdPath = $request->hasFile('valid_id')
            ? $request->file('valid_id')->store($documentDirectory, 'local')
            : $verification?->valid_id_path;
        $selfiePath = $request->hasFile('selfie_with_id')
            ? $request->file('selfie_with_id')->store($documentDirectory, 'local')
            : $verification?->selfie_with_id_path;

        ClientVerification::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                ...collect($validated)->except(['valid_id', 'selfie_with_id'])->all(),
                'valid_id_path' => $validIdPath,
                'selfie_with_id_path' => $selfiePath,
                'status' => ClientVerification::STATUS_PENDING,
                'submitted_at' => now(),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'rejection_reason' => null,
            ],
        );

        return back()->with('success', 'Verification submitted. An administrator will review your information.');
    }
}
