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
        'password',
        'contact_number',
        'age',
        'address',
        'profile_photo_path',
        'terms_accepted_at',
        'terms_version',

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

    public function isClientVerified(): bool
    {
        return $this->clientVerification?->status === ClientVerification::STATUS_APPROVED;
    }
}
