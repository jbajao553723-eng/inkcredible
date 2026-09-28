<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientVerificationRequest;
use App\Models\ClientVerification;
use Illuminate\Http\RedirectResponse;

class ClientVerificationController extends Controller
{
    public function store(StoreClientVerificationRequest $request): RedirectResponse
    {
        $verification = $request->user()->clientVerification;

        if ($verification?->status === ClientVerification::STATUS_APPROVED) {
            return back()->with('error', 'Your account is already verified.');
        }

        if ($verification?->status === ClientVerification::STATUS_PENDING) {
            return back()->with('error', 'Your verification is already awaiting admin review.');
        }

        $validated = $request->validated();

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
