<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanType extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'min_amount',
        'max_amount',
        'interest_rate',
        'due_days',
        'is_active',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function installmentCount(): int
    {
        return match ($this->name) {
            'arawan' => 30,
            'weekly', 'emergency' => 4,
            default => 1,
        };
    }

    public function repaymentPeriodDays(): int
    {
        return match ($this->name) {
            'arawan' => 30,
            'weekly', 'emergency' => 28,
            default => max(1, (int) $this->due_days),
        };
    }
}
