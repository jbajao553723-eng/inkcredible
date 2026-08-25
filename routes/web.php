<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\LoanAdminController;

use App\Models\Loan;
use App\Models\User;
use App\Models\Payment;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD (FIXED - DO NOT USE SERVICE OR VIEWS)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])
    ->get('/dashboard', function () {

        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // ADMIN REDIRECT
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // ✅ FIXED: ALWAYS USE MODEL RELATIONS
        $loans = Loan::with([
                'loanType',
                'payments',
                'paymentSchedules'
            ])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $pendingPayments = Payment::whereHas('loan', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('status', 'pending')
            ->get();

        return view('dashboard', compact('loans', 'pendingPayments'));

    })->name('dashboard');

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->group(function () {

        Route::get('/loan/create', [LoanController::class, 'create'])
            ->name('loan.create');

        Route::post('/loan/store', [LoanController::class, 'store'])
            ->name('loan.store');

        Route::get('/payments', [PaymentController::class, 'index'])
            ->name('payments.index');

        Route::post('/payments', [PaymentController::class, 'store'])
            ->name('payments.store');

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::delete('/profile', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/loans', [LoanAdminController::class, 'index'])
            ->name('loans');

        Route::get('/loan/{id}', [LoanAdminController::class, 'show'])
            ->name('loan.show');

        Route::get('/clients', function () {

            $clients = User::where('role', 'client')
                ->latest()
                ->get();

            return view('admin.clients.index', compact('clients'));
        })->name('clients');

        Route::post('/loan/{id}/approve', [LoanAdminController::class, 'approve'])
            ->name('loan.approve');

        Route::post('/loan/{id}/reject', [LoanAdminController::class, 'reject'])
            ->name('loan.reject');

        Route::get('/payments', [PaymentController::class, 'adminIndex'])
            ->name('payments.index');

        Route::get('/payment/{id}', [PaymentController::class, 'adminShow'])
            ->name('payment.show');

        Route::post('/payment/{id}/approve', [PaymentController::class, 'approve'])
            ->name('payment.approve');

        Route::post('/payment/{id}/reject', [PaymentController::class, 'reject'])
            ->name('payment.reject');
    });

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';