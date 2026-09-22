<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_PAID = 'paid';

    protected $fillable = [
        'user_id',
        'loan_type_id',
        'amount',
        'total_payable',
        'paid_amount',
        'payment_count',
        'status',
        'loan_code',
        'approved_at',
        'disbursed_at',
        'terms_accepted_at',
        'terms_version',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'paid_amount' => 'decimal:2',
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
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status ?? 'pending');
    }

    /*
    |--------------------------------------------------------------------------
    | LOAN TYPE SAFE ACCESS
    |--------------------------------------------------------------------------
    */
    public function getLoanTypeNameAttribute()
    {
        return $this->loanType?->name ?? 'N/A';
    }

    public function getLoanTypeDisplayAttribute()
    {
        return $this->loanType?->display_name ?? 'N/A';
    }

    /*
    |--------------------------------------------------------------------------
    | GOVERNMENT ID
    |--------------------------------------------------------------------------
    */
    public function getGovernmentIdAttribute()
    {
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
    public function getPenaltyAmountAttribute()
    {
        return $this->paymentSchedules()->sum('penalty_amount');
    }

    public function getPenaltyPercentageAttribute()
    {
        return 2.00;
    }

    /*
    |--------------------------------------------------------------------------
    | NEXT PAYMENT
    |--------------------------------------------------------------------------
    */
    public function getNextPaymentDateAttribute()
    {
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
    public function getTotalWithPenalty()
    {
        return ($this->total_payable ?? 0) + $this->penalty_amount;
    }

    public function getRemainingBalance()
    {
        return max(0, $this->getTotalWithPenalty() - ($this->paid_amount ?? 0));
    }

    /*
    |--------------------------------------------------------------------------
    | OVERDUE DAYS
    |--------------------------------------------------------------------------
    */
    public function getOverdueDays()
    {
        $schedule = $this->paymentSchedules()
            ->where('status', PaymentSchedule::STATUS_OVERDUE)
            ->orderBy('due_date')
            ->first();

        if (! $schedule) {
            return 0;
        }

        return Carbon::parse($schedule->due_date)->diffInDays(now());
    }
}
