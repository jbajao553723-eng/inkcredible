<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminAccessController extends Controller
{
    public function index(): View
    {
        $admins = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->with('disabledBy:id,name')
            ->latest()
            ->get();

        $stats = [
            'total' => $admins->count(),
            'active' => $admins->where('is_active', true)->count(),
            'disabled' => $admins->where('is_active', false)->count(),
            'superadmins' => User::where('role', User::ROLE_SUPERADMIN)->where('is_active', true)->count(),
        ];

        return view('admin.access.index', compact('admins', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('createAdmin', [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $fullName = trim($validated['first_name'].' '.$validated['last_name']);

        User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => $fullName,
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'email_verified_at' => now(),
            'contact_number' => $validated['contact_number'] ?: 'Not provided',
            'age' => 18,
            'address' => 'Administrative account',
        ]);

        return redirect()->route('admin.access.index')
            ->with('success', 'Administrator account created. The account can sign in immediately.');
    }

    public function updateStatus(Request $request, User $admin): RedirectResponse
    {
        abort_unless($admin->role === User::ROLE_ADMIN, 404);

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);
        $shouldBeActive = (bool) $validated['is_active'];

        if ($admin->is_active === $shouldBeActive) {
            return back()->with('success', 'No access change was needed for '.$admin->name.'.');
        }

        DB::transaction(function () use ($admin, $request, $shouldBeActive): void {
            $admin->forceFill([
                'is_active' => $shouldBeActive,
                'disabled_at' => $shouldBeActive ? null : now(),
                'disabled_by' => $shouldBeActive ? null : $request->user()->id,
                'remember_token' => $shouldBeActive ? $admin->remember_token : Str::random(60),
            ])->save();

            if (! $shouldBeActive) {
                DB::table('sessions')->where('user_id', $admin->id)->delete();
                DB::table('password_reset_tokens')->where('email', $admin->email)->delete();
            }
        });

        $message = $shouldBeActive
            ? $admin->name.' can now access the administrator workspace.'
            : $admin->name.' has been disabled and signed out of active sessions.';

        return redirect()->route('admin.access.index')->with('success', $message);
    }

    public function update(Request $request, User $admin): RedirectResponse
    {
        abort_unless($admin->role === User::ROLE_ADMIN, 404);

        $request->merge(['editing_admin_id' => $admin->id]);

        $validated = $request->validateWithBag('editAdmin', [
            'editing_admin_id' => ['required', 'integer', Rule::in([$admin->id])],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($admin->id)],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $passwordChanged = filled($validated['password'] ?? null);
        $previousEmail = $admin->email;

        DB::transaction(function () use ($admin, $validated, $passwordChanged, $previousEmail): void {
            $admin->fill([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => trim($validated['first_name'].' '.$validated['last_name']),
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'] ?: 'Not provided',
            ]);

            if ($passwordChanged) {
                $admin->password = $validated['password'];
                $admin->remember_token = Str::random(60);
            }

            $admin->save();

            if ($passwordChanged) {
                DB::table('sessions')->where('user_id', $admin->id)->delete();
                DB::table('password_reset_tokens')->whereIn('email', array_unique([$previousEmail, $admin->email]))->delete();
            }
        });

        $message = $passwordChanged
            ? $admin->name.' was updated and signed out so the new password is required.'
            : $admin->name.' was updated successfully.';

        return redirect()->route('admin.access.index')->with('success', $message);
    }
}
