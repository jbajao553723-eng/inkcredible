<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientVerification;
use App\Models\User;
use App\Notifications\ClientVerificationApprovedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientVerificationAdminController extends Controller
{
    public function index(Request $request): View
    {
        $clients = User::where('role', 'client')
            ->with('clientVerification')
            ->withCount([
                'loans',
                'loans as active_loans_count' => fn ($query) => $query->where('status', 'approved'),
            ])
            ->latest()
            ->get();

        $verifications = ClientVerification::with(['user', 'reviewer'])
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'rejected' THEN 1 ELSE 2 END")
            ->orderByDesc('submitted_at')
            ->get();

        $section = $request->query('section') === 'verifications' ? 'verifications' : 'directory';
        $stats = [
            'clients_with_loans' => $clients->where('loans_count', '>', 0)->count(),
            'active_borrowers' => $clients->where('active_loans_count', '>', 0)->count(),
            'pending_verifications' => $verifications->where('status', ClientVerification::STATUS_PENDING)->count(),
            'approved_verifications' => $verifications->where('status', ClientVerification::STATUS_APPROVED)->count(),
        ];

        return view('admin.clients.index', compact('clients', 'verifications', 'section', 'stats'));
    }

    public function show(ClientVerification $verification): View
    {
        $verification->load(['user.loans', 'reviewer']);

        return view('admin.verifications.show', compact('verification'));
    }

    public function profilePhoto(User $client): StreamedResponse
    {
        abort_unless($client->role === 'client', 404);

        $path = $client->profile_photo_path;

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    public function approve(ClientVerification $verification, Request $request): RedirectResponse
    {
        if ($verification->status !== ClientVerification::STATUS_PENDING) {
            return back()->with('error', 'Only pending verification submissions can be approved.');
        }

        if (! $verification->digital_signature) {
            return back()->with('error', 'The client must provide a digital signature before verification can be approved.');
        }

        $verification->update([
            'status' => ClientVerification::STATUS_APPROVED,
            'payslip_verified_at' => $verification->payslip_path ? now() : null,
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
            'rejection_reason' => null,
        ]);

        $verification->user?->notify(new ClientVerificationApprovedNotification);

        return redirect()->route('admin.clients', ['section' => 'verifications'])
            ->with('success', 'Client verification approved. The client may now request a loan.');
    }

    public function reject(ClientVerification $verification, Request $request): RedirectResponse
    {
        if ($verification->status !== ClientVerification::STATUS_PENDING) {
            return back()->with('error', 'Only pending verification submissions can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $verification->update([
            'status' => ClientVerification::STATUS_REJECTED,
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('admin.clients', ['section' => 'verifications'])
            ->with('success', 'Client verification rejected with a reason for resubmission.');
    }

    public function document(ClientVerification $verification, string $type): StreamedResponse
    {
        $path = match ($type) {
            'valid-id' => $verification->valid_id_path,
            'selfie' => $verification->selfie_with_id_path,
            'payslip' => $verification->payslip_path,
            default => abort(404),
        };

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }
}
