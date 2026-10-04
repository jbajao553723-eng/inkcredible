<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::post('/paymongo/webhook', [PaymentController::class, 'webhook'])
    ->name('payments.webhook');

Route::get('/cron/calculate-loan-penalties', function (Request $request) {
    $secret = (string) config('services.vercel.cron_secret');
    $authorization = (string) $request->header('Authorization');

    abort_unless($secret !== '' && hash_equals('Bearer '.$secret, $authorization), 401);

    return Cache::lock('cron:calculate-loan-penalties', 300)->get(function () {
        $exitCode = Artisan::call('app:calculate-loan-penalties');

        return response()->json([
            'ok' => $exitCode === 0,
            'exit_code' => $exitCode,
        ], $exitCode === 0 ? 200 : 500);
    }) ?? response()->json(['ok' => true, 'skipped' => 'already-running']);
})->name('cron.calculate-loan-penalties');
