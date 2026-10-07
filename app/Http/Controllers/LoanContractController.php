<?php

namespace App\Http\Controllers;

use App\Models\ClientVerification;
use App\Models\Loan;
use App\Notifications\ContractReadyNotification;
use App\Services\LoanRiskAssessmentService;
use App\Services\PdfBranding;
use App\Services\PdfSignatureImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LoanContractController extends Controller
{
    public function send(Loan $loan): RedirectResponse
    {
        abort_unless(request()->user()?->isAdministrator(), 403);

        if ($loan->status !== Loan::STATUS_PENDING) {
            return back()->with('error', 'Only pending loans can receive a contract.');
        }

        if ($loan->signed_contract_path) {
            Storage::disk(config('filesystems.private_disk'))->delete($loan->signed_contract_path);
        }

        $loan->update([
            'contract_sent_at' => now(),
            'contract_signed_at' => null,
            'contract_signature_name' => null,
            'signed_contract_path' => null,
            'signed_contract_original_name' => null,
            'admin_signature' => null,
            'admin_signature_name' => null,
            'admin_signed_at' => null,
            'admin_signed_by' => null,
        ]);

        $loan->user?->notify(new ContractReadyNotification($loan));

        return back()->with('success', 'Contract sent to the client for review and signature.');
    }

    public function show(Request $request, Loan $loan, LoanRiskAssessmentService $riskAssessments): View
    {
        abort_unless($loan->user_id === $request->user()?->id, 403);
        abort_unless($loan->contract_sent_at, 404);
        $loan->load(['user.clientVerification', 'loanType']);
        $riskAssessment = $riskAssessments->assess($loan);

        return view('client.loans.contract', compact('loan', 'riskAssessment'));
    }

    public function download(Request $request, Loan $loan, PdfBranding $branding): Response
    {
        $this->authorizeAccess($request, $loan);
        abort_unless($loan->contract_sent_at, 404);
        $loan->load(['user', 'loanType']);

        return Pdf::loadView('contracts.loan', [
            'loan' => $loan,
            'logoDataUri' => $branding->logoDataUri(),
        ])
            ->setPaper('a4')
            ->download(($loan->loan_code ?: 'loan-'.$loan->id).'-contract.pdf');
    }

    public function downloadSigned(Request $request, Loan $loan): Response
    {
        $this->authorizeAccess($request, $loan);

        abort_unless(
            $loan->signed_contract_path
                && Storage::disk(config('filesystems.private_disk'))->exists($loan->signed_contract_path),
            404
        );

        return Storage::disk(config('filesystems.private_disk'))->download(
            $loan->signed_contract_path,
            ($loan->loan_code ?: 'loan-'.$loan->id).($loan->admin_signed_at ? '-final-signed-contract.pdf' : '-signed-contract.pdf'),
            ['Content-Type' => 'application/pdf']
        );
    }

    public function sign(
        Request $request,
        Loan $loan,
        PdfSignatureImage $signatureImages,
        PdfBranding $branding,
    ): RedirectResponse {
        abort_unless($loan->user_id === $request->user()?->id, 403);

        if ($loan->status !== Loan::STATUS_PENDING || ! $loan->contract_sent_at) {
            return back()->with('error', 'This contract is not available for signature.');
        }

        $request->validate([
            'contract_accepted' => ['accepted'],
        ]);

        $loan->load(['user.clientVerification', 'loanType']);
        $verification = $loan->user?->clientVerification;

        if (! $verification
            || $verification->status !== ClientVerification::STATUS_APPROVED
            || ! $verification->digital_signature) {
            return redirect()->route('profile.verification.edit')
                ->with('error', 'Add your digital signature to verification before signing a contract.');
        }

        $clientSignature = $signatureImages->prepare($verification->digital_signature);

        if (! $clientSignature) {
            return back()->withErrors([
                'contract' => 'Your stored signature could not be prepared for the PDF. Please update your signature and try again.',
            ]);
        }

        $signedAt = now();
        $pdf = Pdf::loadView('contracts.loan', [
            'loan' => $loan,
            'clientSignature' => $clientSignature,
            'signedAt' => $signedAt,
            'logoDataUri' => $branding->logoDataUri(),
        ])->setPaper('a4');
        $path = 'loan-contracts/'.$loan->id.'/'.Str::uuid().'-signed-contract.pdf';

        if (! Storage::disk(config('filesystems.private_disk'))->put($path, $pdf->output())) {
            return back()->withErrors(['contract' => 'The signed contract could not be generated. Please try again.']);
        }

        if ($loan->signed_contract_path) {
            Storage::disk(config('filesystems.private_disk'))->delete($loan->signed_contract_path);
        }

        $loan->update([
            'contract_signature_name' => $loan->user->full_name,
            'contract_signed_at' => $signedAt,
            'signed_contract_path' => $path,
            'signed_contract_original_name' => ($loan->loan_code ?: 'loan-'.$loan->id).'-digitally-signed.pdf',
        ]);

        return redirect()->route('dashboard')->with('success', 'Your contract was digitally signed and sent to the administrator for final approval.');
    }

    private function authorizeAccess(Request $request, Loan $loan): void
    {
        abort_unless(
            $request->user()?->isAdministrator() || $loan->user_id === $request->user()?->id,
            403
        );
    }
}
