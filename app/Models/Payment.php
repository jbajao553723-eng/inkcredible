<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    protected $fillable = [
        'loan_id',
        'user_id',
        'amount',
        'reference',
        'currency',
        'method',
        'provider',
        'provider_reference',
        'paymongo_session_id',
        'paymongo_payment_id',
        'checkout_url',
        'proof',
        'status',
        'paid_at',
        'webhook_event_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Payment $payment): void {
            if (! in_array($payment->status, self::STATUSES, true)) {
                throw new \InvalidArgumentException('Payment status must be pending, approved, or rejected.');
            }
        });
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'gcash', 'qrph' => 'GCash / QR Ph',
            'paymaya' => 'Maya',
            'bank_transfer', 'dob', 'brankas' => 'Bank transfer',
            'cash' => 'Cash',
            default => str($this->method ?: 'Not recorded')->replace('_', ' ')->title()->toString(),
        };
    }

    public function getMethodGroupAttribute(): string
    {
        return match ($this->method) {
            'qrph' => 'gcash',
            'dob', 'brankas' => 'bank_transfer',
            default => $this->method,
        };
    }
}
