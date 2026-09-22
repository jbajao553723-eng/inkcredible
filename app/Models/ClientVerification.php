<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientVerification extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'status',
        'employment_status',
        'company_name',
        'job_title',
        'monthly_income',
        'employment_length_months',
        'source_of_income',
        'valid_id_type',
        'valid_id_number',
        'valid_id_path',
        'selfie_with_id_path',
        'additional_information',
        'rejection_reason',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'monthly_income' => 'decimal:2',
        'valid_id_number' => 'encrypted',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
