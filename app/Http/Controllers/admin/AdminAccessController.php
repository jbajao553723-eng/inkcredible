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
    public function index(Request $request): View
    {
        $admins = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->with('disabledBy:id,name')
            ->latest()
            ->get();

        $clients = User::query()
            ->where('role', User::ROLE_CLIENT)
            ->with([
                'clientVerification:id,user_id,status',
                'disabledBy:id,name',
            ])
            ->latest()
            ->get();

        $managedUsers = $admins->concat($clients);
        $section = $request->string('section')->toString() === 'clients' ? 'clients' : 'admins';

        $stats = [
            'total' => $managedUsers->count(),
            'admins' => $admins->count(),
            'clients' => $clients->count(),
            'disabled' => $managedUsers->where('is_active', false)->count(),
            'superadmins' => User::where('role', User::ROLE_SUPERADMIN)->where('is_active', true)->count(),
        ];

        return view('admin.access.index', [
            'admins' => $admins,
            'clients' => $clients,
            'section' => $section,
            'stats' => $stats,
            'provinces' => config('philippine_locations', []),
        ]);
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

        return $this->changeStatus($request, $admin);
    }

    public function updateUserStatus(Request $request, User $user): RedirectResponse
    {
        $this->ensureManageable($user);

        return $this->changeStatus($request, $user);
    }

    private function changeStatus(Request $request, User $user): RedirectResponse
    {
        $section = $user->role === User::ROLE_CLIENT ? 'clients' : 'admins';

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);
        $shouldBeActive = (bool) $validated['is_active'];

        if ($user->is_active === $shouldBeActive) {
            return redirect()->route('admin.access.index', ['section' => $section])
                ->with('success', 'No access change was needed for '.$user->name.'.');
        }

        DB::transaction(function () use ($user, $request, $shouldBeActive): void {
            $user->forceFill([
                'is_active' => $shouldBeActive,
                'disabled_at' => $shouldBeActive ? null : now(),
                'disabled_by' => $shouldBeActive ? null : $request->user()->id,
                'remember_token' => $shouldBeActive ? $user->remember_token : Str::random(60),
            ])->save();

            if (! $shouldBeActive) {
                DB::table('sessions')->where('user_id', $user->id)->delete();
                DB::table('password_reset_tokens')->where('email', $user->email)->delete();
            }
        });

        $workspace = $user->role === User::ROLE_CLIENT ? 'client account' : 'administrator workspace';
        $message = $shouldBeActive
            ? $user->name.' can now access the '.$workspace.'.'
            : $user->name.' has been disabled and signed out of active sessions.';

        return redirect()->route('admin.access.index', ['section' => $section])->with('success', $message);
    }

    public function update(Request $request, User $admin): RedirectResponse
    {
        abort_unless($admin->role === User::ROLE_ADMIN, 404);

        return $this->updateManagedUser($request, $admin, 'editAdmin');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $this->ensureManageable($user);

        return $this->updateManagedUser($request, $user, 'editUser');
    }

    private function updateManagedUser(Request $request, User $user, string $errorBag): RedirectResponse
    {
        $request->merge(['editing_user_id' => $user->id]);

        $rules = [
            'editing_user_id' => ['required', 'integer', Rule::in([$user->id])],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'contact_number' => [$user->role === User::ROLE_CLIENT ? 'required' : 'nullable', 'string', 'max:30'],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ];

        if ($user->role === User::ROLE_CLIENT) {
            $rules += [
                'age' => ['required', 'integer', 'min:18', 'max:120'],
                'street_address' => ['required', 'string', 'max:255'],
                'barangay' => ['required', 'string', 'max:150'],
                'city_municipality' => ['required', 'string', 'max:150'],
                'province' => ['required', 'string', 'max:100', Rule::in(config('philippine_locations', []))],
            ];
        }

        $validated = $request->validateWithBag($errorBag, $rules);

        $passwordChanged = filled($validated['password'] ?? null);
        $previousEmail = $user->email;
        $emailChanged = strcasecmp($previousEmail, $validated['email']) !== 0;
        $requiresReauthentication = $passwordChanged || $emailChanged;

        DB::transaction(function () use ($user, $validated, $passwordChanged, $previousEmail, $requiresReauthentication): void {
            $profile = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'name' => trim($validated['first_name'].' '.$validated['last_name']),
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'] ?: 'Not provided',
            ];

            if ($user->role === User::ROLE_CLIENT) {
                $profile += [
                    'age' => $validated['age'],
                    'street_address' => trim($validated['street_address']),
                    'barangay' => $validated['barangay'],
                    'city_municipality' => $validated['city_municipality'],
                    'province' => $validated['province'],
                    'address' => implode(', ', [
                        trim($validated['street_address']),
                        $validated['barangay'],
                        $validated['city_municipality'],
                        $validated['province'],
                    ]),
                ];
            }

            $user->fill($profile);

            if ($passwordChanged) {
                $user->password = $validated['password'];
            }

            if ($requiresReauthentication) {
                $user->remember_token = Str::random(60);
            }

            $user->save();

            if ($requiresReauthentication) {
                DB::table('sessions')->where('user_id', $user->id)->delete();
                DB::table('password_reset_tokens')->whereIn('email', array_unique([$previousEmail, $user->email]))->delete();
            }
        });

        $message = $requiresReauthentication
            ? $user->name.' was updated and signed out so the new sign-in details are required.'
            : $user->name.' was updated successfully.';

        return redirect()->route('admin.access.index', [
            'section' => $user->role === User::ROLE_CLIENT ? 'clients' : 'admins',
        ])->with('success', $message);
    }

    private function ensureManageable(User $user): void
    {
        abort_unless(in_array($user->role, [User::ROLE_ADMIN, User::ROLE_CLIENT], true), 404);
    }
}
