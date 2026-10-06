<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Notifications\LoanApprovedNotification;
use App\Rules\DigitalSignature;
use App\Services\LoanApprovalService;
use App\Services\LoanRiskAssessmentService;
use App\Services\PdfSignatureImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LoanAdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ALL LOANS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        // FIX: eager load EVERYTHING needed for dashboard consistency
        $loans = Loan::with(['user', 'loanType', 'paymentSchedules'])
            ->latest()
            ->get();

        return view('admin.loans.index', compact('loans'));
    }

    /*
    |--------------------------------------------------------------------------
    | VIEW SINGLE LOAN
    |--------------------------------------------------------------------------
    */
    public function show(Loan $loan, LoanRiskAssessmentService $riskAssessments)
    {
        $loan->load(['user.clientVerification', 'loanType', 'paymentSchedules', 'loanDocuments', 'payments']);
        $riskAssessment = $riskAssessments->assess($loan);

        return view('admin.loans.show', compact('loan', 'riskAssessment'));
    }

    public function governmentId(Loan $loan): StreamedResponse
    {
        $document = $loan->loanDocuments()
            ->where('document_type', 'government_id')
            ->latest()
            ->firstOrFail();

        $disk = Storage::disk(config('filesystems.public_disk'));

        abort_unless($disk->exists($document->file_path), 404);

        return $disk->response(
            $document->file_path,
            $document->original_filename ?: basename($document->file_path),
            [
                'Cache-Control' => 'private, no-store',
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE LOAN
    |--------------------------------------------------------------------------
    */
    public function approve(
        Request $request,
        Loan $loan,
        LoanApprovalService $approvals,
        PdfSignatureImage $signatureImages,
    ): RedirectResponse {
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Only pending loan requests can be approved.');
        }

        if (! $loan->contract_sent_at) {
            return back()->with('error', 'Send the final contract to the client before approval.');
        }

        if (! $loan->contract_signed_at
            || ! $loan->signed_contract_path
            || ! Storage::disk(config('filesystems.private_disk'))->exists($loan->signed_contract_path)) {
            return back()->with('error', 'The client must digitally sign and submit the contract before final approval.');
        }

        $validated = $request->validate([
            'admin_signature' => ['required', 'string', 'max:500000', new DigitalSignature],
            'approval_accepted' => ['accepted'],
        ]);

        $loan->load(['user.clientVerification', 'loanType']);
        $storedClientSignature = $loan->user?->clientVerification?->digital_signature;

        if (! $storedClientSignature) {
            return back()->with('error', 'The verified client signature is unavailable. The contract cannot be finalized.');
        }

        $clientSignature = $signatureImages->prepare($storedClientSignature);
        $adminSignature = $signatureImages->prepare($validated['admin_signature']);

        if (! $clientSignature || ! $adminSignature) {
            return back()->withErrors([
                'admin_signature' => 'A stored signature could not be prepared for the final PDF. Please redraw the administrator signature and try again.',
            ]);
        }

        $adminSignedAt = now();
        $adminName = $request->user()->full_name;
        $pdf = Pdf::loadView('contracts.loan', [
            'loan' => $loan,
            'clientSignature' => $clientSignature,
            'signedAt' => $loan->contract_signed_at,
            'adminSignature' => $adminSignature,
            'adminSignatureName' => $adminName,
            'adminSignedAt' => $adminSignedAt,
        ])->setPaper('a4');
        $finalPath = 'loan-contracts/'.$loan->id.'/'.Str::uuid().'-final-contract.pdf';

        if (! Storage::disk(config('filesystems.private_disk'))->put($finalPath, $pdf->output())) {
            return back()->withErrors(['admin_signature' => 'The final signed contract could not be generated. Please try again.']);
        }

        $previousPath = $loan->signed_contract_path;

        $loan->update([
            'admin_signature' => $validated['admin_signature'],
            'admin_signature_name' => $adminName,
            'admin_signed_at' => $adminSignedAt,
            'admin_signed_by' => $request->user()->id,
            'signed_contract_path' => $finalPath,
            'signed_contract_original_name' => ($loan->loan_code ?: 'loan-'.$loan->id).'-final-signed-contract.pdf',
        ]);

        if ($previousPath !== $finalPath) {
            Storage::disk(config('filesystems.private_disk'))->delete($previousPath);
        }

        $approvals->approve($loan);
        $loan->refresh();
        $loan->user?->notify(new LoanApprovedNotification($loan));

        return back()->with('success', 'The contract was digitally signed by the administrator and the loan was final-approved.');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT LOAN
    |--------------------------------------------------------------------------
    */
    public function reject(Request $request, Loan $loan): RedirectResponse
    {
        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Only pending loan requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500', 'regex:/\S/'],
        ]);

        $loan->update([
            'status' => Loan::STATUS_REJECTED,
            'rejection_reason' => trim($validated['rejection_reason']),
        ]);

        return back()->with('success', 'Loan rejected with a reason for the client.');
    }
}
