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
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'action' => ['nullable', 'string', 'max:80'],
            'event_action' => ['nullable', 'string', 'max:80'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $selectedAction = $validated['event_action'] ?? $validated['action'] ?? null;

        $logs = AuditLog::with('user')
            ->when($validated['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($selectedAction, fn ($query, $action) => $query->where('action', $action))
            ->when($validated['date_from'] ?? null, fn ($query, $date) => $query->where('occurred_at', '>=', Carbon::parse($date, 'Asia/Manila')->startOfDay()->utc()))
            ->when($validated['date_to'] ?? null, fn ($query, $date) => $query->where('occurred_at', '<=', Carbon::parse($date, 'Asia/Manila')->endOfDay()->utc()))
            ->latest('occurred_at')
            ->paginate(30)
            ->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $actions = AuditLog::query()->distinct()->orderBy('action')->pluck('action');
        $todayStart = now('Asia/Manila')->startOfDay()->utc();
        $todayEnd = now('Asia/Manila')->endOfDay()->utc();
        $summary = [
            'today' => AuditLog::whereBetween('occurred_at', [$todayStart, $todayEnd])->count(),
            'logins' => AuditLog::where('action', 'login')->count(),
            'failed_logins' => AuditLog::where('action', 'login_failed')->count(),
        ];

        return view('admin.audit-logs.index', compact('logs', 'users', 'actions', 'summary'));
    }
}
