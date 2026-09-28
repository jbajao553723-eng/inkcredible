<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDocument extends Model
{
    protected $fillable = [
        'loan_id',
        'document_type',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'is_verified',
        'notes',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_verified' => 'boolean',
    ];

    const TYPE_ID = 'government_id';

    const TYPE_PROOF_OF_INCOME = 'proof_of_income';

    const TYPE_BANK_STATEMENT = 'bank_statement';

    const TYPE_COLLATERAL = 'collateral';

    const TYPE_OTHER = 'other';

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }
}
