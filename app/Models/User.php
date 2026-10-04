<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_CLIENT = 'client';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_SUPERADMIN = 'superadmin';

    protected $attributes = [
        'role' => self::ROLE_CLIENT,
        'is_active' => true,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'contact_number',
        'age',
        'address',
        'street_address',
        'barangay',
        'city_municipality',
        'province',
        'profile_photo_path',
        'ui_preferences',
        'terms_accepted_at',
        'terms_version',
        'role',
        'is_active',
        'disabled_at',
        'disabled_by',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'terms_accepted_at' => 'datetime',
            'is_active' => 'boolean',
            'disabled_at' => 'datetime',
            'ui_preferences' => 'array',
        ];
    }

    /**
     * Get the user's display name from the separated name fields.
     */
    public function getFullNameAttribute(): string
    {
        $fullName = trim($this->first_name.' '.$this->last_name);

        return $fullName !== '' ? $fullName : $this->name;
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function clientVerification()
    {
        return $this->hasOne(ClientVerification::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function disabledBy()
    {
        return $this->belongsTo(self::class, 'disabled_by');
    }

    public function isAdministrator(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPERADMIN], true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isClientVerified(): bool
    {
        return $this->clientVerification?->status === ClientVerification::STATUS_APPROVED;
    }
}
