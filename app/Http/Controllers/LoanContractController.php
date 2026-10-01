<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Notifications\ContractReadyNotification;
use App\Services\LoanRiskAssessmentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            Storage::disk('local')->delete($loan->signed_contract_path);
        }

        $loan->update([
            'contract_sent_at' => now(),
            'contract_signed_at' => null,
            'contract_signature_name' => null,
            'signed_contract_path' => null,
            'signed_contract_original_name' => null,
        ]);

        $loan->user?->notify(new ContractReadyNotification($loan));

        return back()->with('success', 'Contract sent to the client for review and signature.');
    }

    public function show(Request $request, Loan $loan, LoanRiskAssessmentService $riskAssessments): View
    {
        abort_unless($loan->user_id === $request->user()?->id, 403);
        abort_unless($loan->contract_sent_at, 404);
        $loan->load(['user', 'loanType']);
        $riskAssessment = $riskAssessments->assess($loan);

        return view('client.loans.contract', compact('loan', 'riskAssessment'));
    }

    public function download(Request $request, Loan $loan): Response
    {
        $this->authorizeAccess($request, $loan);
        abort_unless($loan->contract_sent_at, 404);
        $loan->load(['user', 'loanType']);

        return Pdf::loadView('contracts.loan', ['loan' => $loan])
            ->setPaper('a4')
            ->download(($loan->loan_code ?: 'loan-'.$loan->id).'-contract.pdf');
    }

    public function downloadSigned(Request $request, Loan $loan): Response
    {
        $this->authorizeAccess($request, $loan);

        abort_unless(
            $loan->signed_contract_path
                && Storage::disk('local')->exists($loan->signed_contract_path),
            404
        );

        return Storage::disk('local')->download(
            $loan->signed_contract_path,
            ($loan->loan_code ?: 'loan-'.$loan->id).'-signed-contract.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function sign(Request $request, Loan $loan): RedirectResponse
    {
        abort_unless($loan->user_id === $request->user()?->id, 403);

        if ($loan->status !== Loan::STATUS_PENDING || ! $loan->contract_sent_at) {
            return back()->with('error', 'This contract is not available for signature.');
        }

        $validated = $request->validate([
            'signed_contract' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'contract_accepted' => ['accepted'],
        ]);

        $uploadedContract = $validated['signed_contract'];
        $path = $uploadedContract->store('loan-contracts/'.$loan->id, 'local');

        if (! $path) {
            return back()->withErrors(['signed_contract' => 'The signed contract could not be saved. Please try again.']);
        }

        if ($loan->signed_contract_path) {
            Storage::disk('local')->delete($loan->signed_contract_path);
        }

        $loan->update([
            'contract_signature_name' => $loan->user->full_name,
            'contract_signed_at' => now(),
            'signed_contract_path' => $path,
            'signed_contract_original_name' => $uploadedContract->getClientOriginalName(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Your signed PDF was returned to the administrator for review and final approval.');
    }

    private function authorizeAccess(Request $request, Loan $loan): void
    {
        abort_unless(
            $request->user()?->isAdministrator() || $loan->user_id === $request->user()?->id,
            403
        );
    }
}
