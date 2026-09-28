<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Loan extends Model
{
    use HasFactory;

    public const DAILY_PENALTY_RATE = 5.00;

    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_PAID = 'paid';

    protected $fillable = [
        'user_id',
        'loan_type_id',
        'purpose',
        'amount',
        'total_payable',
        'installment_count',
        'repayment_period_days',
        'paid_amount',
        'payment_count',
        'status',
        'rejection_reason',
        'loan_code',
        'approved_at',
        'disbursed_at',
        'terms_accepted_at',
        'terms_version',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'installment_count' => 'integer',
        'repayment_period_days' => 'integer',
        'paid_amount' => 'decimal:2',
        'penalty_percentage' => 'decimal:2',
        'is_overdue' => 'boolean',
        'last_penalty_calculated_date' => 'date',
        'approved_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'terms_accepted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class)
            ->orderBy('installment_number');
    }

    public function loanDocuments()
    {
        return $this->hasMany(LoanDocument::class);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS FIX (IMPORTANT)
    |--------------------------------------------------------------------------
    | ❌ REMOVE MUTATION — IT BREAKS BLADE + FILTERING
    */
    // REMOVED getStatusAttribute completely

    /*
    |--------------------------------------------------------------------------
    | SAFE STATUS DISPLAY (NEW)
    |--------------------------------------------------------------------------
    */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status ?? 'pending');
    }

    /*
    |--------------------------------------------------------------------------
    | LOAN TYPE SAFE ACCESS
    |--------------------------------------------------------------------------
    */
    public function getLoanTypeNameAttribute(): string
    {
        return $this->loanType?->name ?? 'N/A';
    }

    public function getLoanTypeDisplayAttribute(): string
    {
        return $this->loanType?->display_name ?? 'N/A';
    }

    /*
    |--------------------------------------------------------------------------
    | GOVERNMENT ID
    |--------------------------------------------------------------------------
    */
    public function getGovernmentIdAttribute(): ?string
    {
        if ($this->relationLoaded('loanDocuments')) {
            return $this->loanDocuments
                ->where('document_type', LoanDocument::TYPE_ID)
                ->sortByDesc('id')
                ->first()?->file_path;
        }

        return $this->loanDocuments()
            ->where('document_type', LoanDocument::TYPE_ID)
            ->latest()
            ->value('file_path');
    }

    /*
    |--------------------------------------------------------------------------
    | PENALTY
    |--------------------------------------------------------------------------
    */
    public function getPenaltyAmountAttribute(): float
    {
        $total = $this->relationLoaded('paymentSchedules')
            ? $this->paymentSchedules->sum('penalty_amount')
            : $this->paymentSchedules()->sum('penalty_amount');

        return (float) $total;
    }

    public function getPenaltyPercentageAttribute(): float
    {
        return self::DAILY_PENALTY_RATE;
    }

    /**
     * Apply a simple daily penalty to the unpaid part of every overdue installment.
     */
    public function calculatePenalty(?Carbon $asOf = null): float
    {
        $calculationDate = ($asOf ?? Carbon::now('Asia/Manila'))->copy()->startOfDay();

        return DB::transaction(function () use ($calculationDate) {
            $loan = self::whereKey($this->getKey())->lockForUpdate()->firstOrFail();
            $unallocatedPaidAmount = max(0, (float) $loan->paid_amount);
            $totalPenalty = 0.0;
            $hasOverdueInstallment = false;

            $schedules = $loan->paymentSchedules()
                ->orderBy('installment_number')
                ->lockForUpdate()
                ->get();

            foreach ($schedules as $schedule) {
                $scheduledAmount = (float) $schedule->scheduled_amount;
                $principalPaid = min($unallocatedPaidAmount, $scheduledAmount);
                $unallocatedPaidAmount -= $principalPaid;
                $unpaidPrincipal = max(0, $scheduledAmount - $principalPaid);
                $dueDate = $schedule->due_date->copy()->startOfDay();
                $daysOverdue = $unpaidPrincipal > 0 && $dueDate->lt($calculationDate)
                    ? (int) $dueDate->diffInDays($calculationDate)
                    : 0;
                $penaltyAmount = round(
                    $unpaidPrincipal * (self::DAILY_PENALTY_RATE / 100) * $daysOverdue,
                    2
                );

                $status = $unpaidPrincipal <= 0
                    ? PaymentSchedule::STATUS_PAID
                    : ($daysOverdue > 0 ? PaymentSchedule::STATUS_OVERDUE : PaymentSchedule::STATUS_PENDING);

                $schedule->update([
                    'paid_amount' => round($principalPaid, 2),
                    'penalty_amount' => $penaltyAmount,
                    'status' => $status,
                ]);

                $totalPenalty += $penaltyAmount;
                $hasOverdueInstallment = $hasOverdueInstallment || $daysOverdue > 0;
            }

            $loan->forceFill([
                'penalty_percentage' => self::DAILY_PENALTY_RATE,
                'penalty_amount' => round($totalPenalty, 2),
                'is_overdue' => $hasOverdueInstallment,
                'last_penalty_calculated_date' => $calculationDate->toDateString(),
            ])->saveQuietly();

            $this->refresh();

            return round($totalPenalty, 2);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | NEXT PAYMENT
    |--------------------------------------------------------------------------
    */
    public function getNextPaymentDateAttribute(): ?Carbon
    {
        if ($this->relationLoaded('paymentSchedules')) {
            return $this->paymentSchedules
                ->where('status', '!=', PaymentSchedule::STATUS_PAID)
                ->sortBy('due_date')
                ->first()?->due_date;
        }

        $schedule = $this->paymentSchedules()
            ->where('status', '!=', PaymentSchedule::STATUS_PAID)
            ->orderBy('due_date')
            ->first();

        return $schedule?->due_date;
    }

    /*
    |--------------------------------------------------------------------------
    | BALANCE
    |--------------------------------------------------------------------------
    */
    public function getTotalWithPenalty(): float
    {
        return ($this->total_payable ?? 0) + $this->penalty_amount;
    }

    public function getRemainingBalance(): float
    {
        return max(0, $this->getTotalWithPenalty() - ($this->paid_amount ?? 0));
    }

    /*
    |--------------------------------------------------------------------------
    | OVERDUE DAYS
    |--------------------------------------------------------------------------
    */
    public function getOverdueDays(): int
    {
        $schedule = $this->relationLoaded('paymentSchedules')
            ? $this->paymentSchedules
                ->where('status', PaymentSchedule::STATUS_OVERDUE)
                ->sortBy('due_date')
                ->first()
            : $this->paymentSchedules()
                ->where('status', PaymentSchedule::STATUS_OVERDUE)
                ->orderBy('due_date')
                ->first();

        if (! $schedule) {
            return 0;
        }

        return Carbon::parse($schedule->due_date)->diffInDays(now());
    }
}
