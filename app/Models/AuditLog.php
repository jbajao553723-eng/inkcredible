<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'route',
        'method',
        'ip_address',
        'user_agent',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Audit logging must never interrupt the business action being recorded.
     */
    public static function record(string $action, string $description, ?User $user = null, array $metadata = []): void
    {
        try {
            $request = request();

            static::create([
                'user_id' => $user?->id,
                'action' => $action,
                'description' => $description,
                'route' => $request->route()?->getName(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
                'metadata' => $metadata ?: null,
                'occurred_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
