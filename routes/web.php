<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ClientReportController;
use App\Http\Controllers\Admin\ClientVerificationAdminController;
use App\Http\Controllers\Admin\LoanAdminController;
use App\Http\Controllers\ClientVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::view('/', 'auth.login')->middleware('guest')->name('home');

Route::view('/terms-and-conditions', 'legal.terms')->name('terms');
Route::view('/loan-terms', 'legal.loan-terms')->name('loan.terms');

/*
|--------------------------------------------------------------------------
| DASHBOARD (FIXED - DO NOT USE SERVICE OR VIEWS)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->group(function () {

        Route::get('/loan/create', [LoanController::class, 'create'])
            ->middleware('client.verified')
            ->name('loan.create');

        Route::post('/loan/store', [LoanController::class, 'store'])
            ->middleware('client.verified')
            ->name('loan.store');

        Route::get('/payments', [PaymentController::class, 'index'])
            ->name('payments.index');

        Route::post('/payments', [PaymentController::class, 'store'])
            ->name('payments.store');

        Route::get('/payments/success/{payment}', [PaymentController::class, 'success'])
            ->name('payments.success');

        Route::get('/payments/cancel/{payment}', [PaymentController::class, 'cancel'])
            ->name('payments.cancel');

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::get('/profile/photo', [ProfileController::class, 'photo'])
            ->name('profile.photo');

        Route::get('/profile/verification', [ProfileController::class, 'verification'])
            ->name('profile.verification.edit');

        Route::get('/profile/security', [ProfileController::class, 'security'])
            ->name('profile.security.edit');

        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::post('/profile/verification', [ClientVerificationController::class, 'store'])
            ->name('profile.verification.store');

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

        Route::get('/loan/{loan}', [LoanAdminController::class, 'show'])
            ->name('loan.show');

        Route::get('/clients', [ClientVerificationAdminController::class, 'index'])
            ->name('clients');

        Route::get('/client/{client}/profile-photo', [ClientVerificationAdminController::class, 'profilePhoto'])
            ->name('clients.photo');

        Route::get('/reports', [ClientReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/reports/business/download', [ClientReportController::class, 'downloadBusiness'])
            ->name('reports.business.download');

        Route::get('/reports/{client}/download', [ClientReportController::class, 'download'])
            ->name('reports.download');

        Route::get('/verifications', fn () => redirect()->route('admin.clients', ['section' => 'verifications']))
            ->name('verifications.index');

        Route::get('/verification/{verification}', [ClientVerificationAdminController::class, 'show'])
            ->name('verifications.show');

        Route::post('/verification/{verification}/approve', [ClientVerificationAdminController::class, 'approve'])
            ->name('verifications.approve');

        Route::post('/verification/{verification}/reject', [ClientVerificationAdminController::class, 'reject'])
            ->name('verifications.reject');

        Route::get('/verification/{verification}/document/{type}', [ClientVerificationAdminController::class, 'document'])
            ->name('verifications.document');

        Route::post('/loan/{loan}/approve', [LoanAdminController::class, 'approve'])
            ->name('loan.approve');

        Route::post('/loan/{loan}/reject', [LoanAdminController::class, 'reject'])
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
