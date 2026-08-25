<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSchedule extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_number',
        'scheduled_amount',
        'due_date',
        'paid_amount',
        'paid_date',
        'status',
        'penalty_amount',
        'notes',
    ];

    protected $casts = [
        'scheduled_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_PARTIAL = 'partial';

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }

    public function activePenalties()
    {
        return $this->hasMany(Penalty::class)->where('status', Penalty::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
                    ->where('status', '!=', self::STATUS_PAID);
    }

    public function isOverdue()
    {
        return $this->status !== self::STATUS_PAID && $this->due_date < now();
    }

    public function getRemainingAmountAttribute()
    {
        return $this->scheduled_amount + $this->penalty_amount - ($this->paid_amount ?? 0);
    }
}
