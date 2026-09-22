<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
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

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
