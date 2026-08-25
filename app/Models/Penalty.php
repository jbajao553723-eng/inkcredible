<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    protected $fillable = [
        'loan_id',
        'payment_schedule_id',
        'type',
        'amount',
        'applied_date',
        'reason',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'applied_date' => 'date',
    ];

    const TYPE_LATE_PAYMENT = 'late_payment';
    const TYPE_ADDITIONAL_FEE = 'additional_fee';
    const TYPE_PROCESSING_FEE = 'processing_fee';

    const STATUS_ACTIVE = 'active';
    const STATUS_WAIVED = 'waived';
    const STATUS_PAID = 'paid';

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function paymentSchedule()
    {
        return $this->belongsTo(PaymentSchedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeWaived($query)
    {
        return $query->where('status', self::STATUS_WAIVED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeForLoan($query, $loanId)
    {
        return $query->where('loan_id', $loanId);
    }

    public function waivePenalty($reason = null)
    {
        $updateData = ['status' => self::STATUS_WAIVED];

        if ($reason) {
            $updateData['reason'] = $this->reason . ' (Waived: ' . $reason . ')';
        }

        $this->update($updateData);

        // Update the payment schedule penalty amount
        if ($this->paymentSchedule) {
            $remainingPenalties = self::where('payment_schedule_id', $this->payment_schedule_id)
                ->where('status', self::STATUS_ACTIVE)
                ->sum('amount');

            $this->paymentSchedule->update([
                'penalty_amount' => $remainingPenalties
            ]);
        }
    }

    public function markAsPaid()
    {
        $this->update(['status' => self::STATUS_PAID]);
    }
}
