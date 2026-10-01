<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'action' => ['nullable', 'string', 'max:80'],
            'event_action' => ['nullable', 'string', 'max:80'],
            'severity' => ['nullable', 'in:high,medium,info'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $selectedAction = $validated['event_action'] ?? $validated['action'] ?? null;
        $search = trim($validated['q'] ?? '');

        $logs = AuditLog::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('description', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhere('route', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($user) => $user
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($validated['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($selectedAction, fn ($query, $action) => $query->where('action', $action))
            ->when(($validated['severity'] ?? null) === 'high', fn ($query) => $query
                ->where(fn ($query) => $query->whereIn('action', ['login_failed', 'authorization_failed'])->orWhere('action', 'like', '%reject%')))
            ->when(($validated['severity'] ?? null) === 'medium', fn ($query) => $query
                ->where(fn ($query) => $query->where('action', 'logout')->orWhere('action', 'like', 'admin_access_%')))
            ->when(($validated['severity'] ?? null) === 'info', fn ($query) => $query
                ->where('action', '!=', 'login_failed')
                ->where('action', '!=', 'authorization_failed')
                ->where('action', 'not like', '%reject%')
                ->where('action', '!=', 'logout')
                ->where('action', 'not like', 'admin_access_%'))
            ->when($validated['date_from'] ?? null, fn ($query, $date) => $query->where('occurred_at', '>=', Carbon::parse($date, 'Asia/Manila')->startOfDay()->utc()))
            ->when($validated['date_to'] ?? null, fn ($query, $date) => $query->where('occurred_at', '<=', Carbon::parse($date, 'Asia/Manila')->endOfDay()->utc()))
            ->latest('occurred_at')
            ->paginate(30)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');
        $todayStart = now('Asia/Manila')->startOfDay()->utc();
        $todayEnd = now('Asia/Manila')->endOfDay()->utc();
        $last24Hours = now()->subDay();
        $summary = [
            'today' => AuditLog::whereBetween('occurred_at', [$todayStart, $todayEnd])->count(),
            'failed_logins' => AuditLog::where('action', 'login_failed')->where('occurred_at', '>=', $last24Hours)->count(),
            'source_ips' => AuditLog::where('action', 'login_failed')->where('occurred_at', '>=', $last24Hours)->whereNotNull('ip_address')->distinct()->count('ip_address'),
            'privileged_changes' => AuditLog::where('action', 'like', 'admin_access_%')->where('occurred_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.audit-logs.index', compact('logs', 'users', 'actions', 'summary', 'search'));
    }
}
