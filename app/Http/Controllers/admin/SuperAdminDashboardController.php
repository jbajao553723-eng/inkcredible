<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SuperAdminDashboardController extends Controller
{
    public function index(): View
    {
        $admins = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->latest()
            ->get();

        $todayStart = now('Asia/Manila')->startOfDay()->utc();
        $todayEnd = now('Asia/Manila')->endOfDay()->utc();
        $last24Hours = now()->subDay();
        $lastSevenDays = now()->subDays(7);
        $failedLoginQuery = AuditLog::where('action', 'login_failed')->where('occurred_at', '>=', $last24Hours);
        $repeatedFailureSources = (clone $failedLoginQuery)
            ->whereNotNull('ip_address')
            ->select('ip_address', DB::raw('COUNT(*) as attempts'))
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) >= 3')
            ->get();
        $activeAdminSessions = config('session.driver') === 'database' && Schema::hasTable('sessions')
            ? DB::table('sessions')->join('users', 'sessions.user_id', '=', 'users.id')
                ->whereIn('users.role', [User::ROLE_ADMIN, User::ROLE_SUPERADMIN])
                ->count()
            : null;

        $stats = [
            'total_admins' => $admins->count(),
            'active_admins' => $admins->where('is_active', true)->count(),
            'disabled_admins' => $admins->where('is_active', false)->count(),
            'events_today' => AuditLog::whereBetween('occurred_at', [$todayStart, $todayEnd])->count(),
            'failed_logins_today' => AuditLog::where('action', 'login_failed')
                ->whereBetween('occurred_at', [$todayStart, $todayEnd])
                ->count(),
            'failed_logins_24h' => (clone $failedLoginQuery)->count(),
            'failure_sources_24h' => (clone $failedLoginQuery)->whereNotNull('ip_address')->distinct()->count('ip_address'),
            'repeat_failure_sources' => $repeatedFailureSources->count(),
            'authorization_failures_24h' => AuditLog::where('action', 'authorization_failed')->where('occurred_at', '>=', $last24Hours)->count(),
            'privileged_changes_7d' => AuditLog::where('action', 'like', 'admin_access_%')->where('occurred_at', '>=', $lastSevenDays)->count(),
            'active_admin_sessions' => $activeAdminSessions,
        ];

        $securityControls = [
            ['name' => 'Login throttling', 'enabled' => true, 'note' => 'Repeated sign-in attempts are rate limited.'],
            ['name' => 'Server-side sessions', 'enabled' => config('session.driver') === 'database', 'note' => 'Database sessions allow immediate administrator revocation.'],
            ['name' => 'HTTP-only session cookie', 'enabled' => (bool) config('session.http_only'), 'note' => 'Prevents client-side scripts from reading the session cookie.'],
            ['name' => 'Secure cookie in production', 'enabled' => app()->environment('local') || (bool) config('session.secure'), 'note' => app()->environment('local') ? 'Required when this application is deployed over HTTPS.' : 'Session cookies must only travel over HTTPS.'],
            ['name' => 'Multi-factor authentication', 'enabled' => false, 'note' => 'Not configured. Add MFA before exposing superadmin access publicly.'],
        ];

        $recentAdmins = $admins->take(5);
        $recentActivity = AuditLog::query()
            ->with('user:id,name,email')
            ->whereIn('action', [
                'login',
                'login_failed',
                'authorization_failed',
                'logout',
                'admin_access_admins_store',
                'admin_access_admins_status',
            ])
            ->latest('occurred_at')
            ->take(8)
            ->get();

        return view('admin.security-dashboard', compact('stats', 'recentAdmins', 'recentActivity', 'securityControls', 'repeatedFailureSources'));
    }
}
