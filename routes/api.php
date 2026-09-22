<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/paymongo/webhook', [PaymentController::class, 'webhook'])
    ->name('payments.webhook');
