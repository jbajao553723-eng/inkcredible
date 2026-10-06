<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientVerificationRequest;
use App\Models\ClientVerification;
use App\Rules\DigitalSignature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
        $disk = Storage::disk(config('filesystems.private_disk'));
        $publicDisk = Storage::disk(config('filesystems.public_disk'));
        $newPaths = [];
        $newProfilePhotoPath = null;
        $previousProfilePhotoPath = $request->user()->profile_photo_path;

        try {
            $newProfilePhotoPath = $request->hasFile('profile_photo')
                ? $request->file('profile_photo')->store('profile-photos', config('filesystems.public_disk'))
                : $previousProfilePhotoPath;
            $validIdPath = $request->hasFile('valid_id')
                ? $request->file('valid_id')->store($documentDirectory, config('filesystems.private_disk'))
                : $verification?->valid_id_path;
            $selfiePath = $request->hasFile('selfie_with_id')
                ? $request->file('selfie_with_id')->store($documentDirectory, config('filesystems.private_disk'))
                : $verification?->selfie_with_id_path;
            $payslipPath = $request->hasFile('payslip')
                ? $request->file('payslip')->store($documentDirectory, config('filesystems.private_disk'))
                : $verification?->payslip_path;

            foreach ([$validIdPath, $selfiePath, $payslipPath] as $path) {
                if ($path && ! in_array($path, [$verification?->valid_id_path, $verification?->selfie_with_id_path, $verification?->payslip_path], true)) {
                    $newPaths[] = $path;
                }
            }

            $digitalSignature = $validated['digital_signature'] ?? $verification?->digital_signature;

            DB::transaction(function () use ($request, $validated, $verification, $newProfilePhotoPath, $validIdPath, $selfiePath, $payslipPath, $digitalSignature): void {
                $request->user()->forceFill([
                    'profile_photo_path' => $newProfilePhotoPath,
                ])->save();

                ClientVerification::updateOrCreate(
                    ['user_id' => $request->user()->id],
                    [
                        ...collect($validated)->except(['profile_photo', 'valid_id', 'selfie_with_id', 'payslip', 'digital_signature'])->all(),
                        'valid_id_path' => $validIdPath,
                        'selfie_with_id_path' => $selfiePath,
                        'payslip_path' => $payslipPath,
                        'payslip_uploaded_at' => $request->hasFile('payslip') ? now() : $verification?->payslip_uploaded_at,
                        'payslip_verified_at' => null,
                        'digital_signature' => $digitalSignature,
                        'signature_captured_at' => array_key_exists('digital_signature', $validated)
                            ? now()
                            : $verification?->signature_captured_at,
                        'status' => ClientVerification::STATUS_PENDING,
                        'submitted_at' => now(),
                        'reviewed_at' => null,
                        'reviewed_by' => null,
                        'rejection_reason' => null,
                    ],
                );
            });
        } catch (Throwable $exception) {
            $disk->delete($newPaths);
            if ($newProfilePhotoPath && $newProfilePhotoPath !== $previousProfilePhotoPath) {
                $publicDisk->delete($newProfilePhotoPath);
            }

            throw $exception;
        }

        $replacedPaths = collect([
            $request->hasFile('valid_id') ? $verification?->valid_id_path : null,
            $request->hasFile('selfie_with_id') ? $verification?->selfie_with_id_path : null,
            $request->hasFile('payslip') ? $verification?->payslip_path : null,
        ])->filter()->all();
        $disk->delete($replacedPaths);
        if ($request->hasFile('profile_photo') && $previousProfilePhotoPath) {
            $publicDisk->delete($previousProfilePhotoPath);
        }

        return back()->with('success', 'Verification submitted. An administrator will review your information.');
    }

    public function storeSignature(Request $request): RedirectResponse
    {
        $verification = $request->user()?->clientVerification;

        abort_unless($request->user()?->role === 'client', 403);

        if (! $verification || $verification->status !== ClientVerification::STATUS_APPROVED) {
            return back()->with('error', 'Complete identity verification before saving a contract signature.');
        }

        if ($verification->digital_signature) {
            return back()->with('error', 'Your verified digital signature is already on file.');
        }

        $validated = $request->validateWithBag('signature', [
            'digital_signature' => ['required', 'string', 'max:500000', new DigitalSignature],
        ]);

        $verification->update([
            'digital_signature' => $validated['digital_signature'],
            'signature_captured_at' => now(),
        ]);

        return back()->with('success', 'Your digital signature is now securely stored and ready for contract signing.');
    }

    public function storePayslip(Request $request): RedirectResponse
    {
        $verification = $request->user()?->clientVerification;

        abort_unless($request->user()?->role === 'client', 403);

        if (! $verification || $verification->status !== ClientVerification::STATUS_APPROVED) {
            return back()->with('error', 'Complete identity verification before updating income evidence.');
        }

        $validated = $request->validateWithBag('payslip', [
            'payslip' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);
        $previousPath = $verification->payslip_path;
        $path = $validated['payslip']->store('client-verifications/'.$request->user()->id, config('filesystems.private_disk'));

        $verification->update([
            'payslip_path' => $path,
            'payslip_uploaded_at' => now(),
            'payslip_verified_at' => null,
            'status' => ClientVerification::STATUS_PENDING,
            'submitted_at' => now(),
            'reviewed_at' => null,
            'reviewed_by' => null,
            'rejection_reason' => null,
        ]);

        if ($previousPath && $previousPath !== $path) {
            Storage::disk(config('filesystems.private_disk'))->delete($previousPath);
        }

        return back()->with('success', 'Your payslip was submitted for administrator verification. The score bonus applies after approval.');
    }
}
