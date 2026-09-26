<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayMongoService
{
    private const API_URL = 'https://api.paymongo.com';

    public function createCheckoutSession(Payment $payment): string
    {
        $secretKey = config('paymongo.secret_key');
        $checkoutMethods = match ($payment->method) {
            'gcash' => [config('paymongo.gcash_checkout_method', 'gcash')],
            'bank_transfer' => config('paymongo.bank_transfer_checkout_methods', ['dob', 'brankas']),
            default => [$payment->method],
        };

        if (! $secretKey) {
            throw new RuntimeException('PayMongo is not configured. Set PAYMONGO_SECRET_KEY first.');
        }

        $allowedCheckoutMethods = ['gcash', 'qrph', 'paymaya', 'dob', 'brankas'];

        if ($checkoutMethods === [] || array_diff($checkoutMethods, $allowedCheckoutMethods) !== []) {
            throw new RuntimeException('The configured PayMongo checkout method is invalid.');
        }

        $response = Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->asJson()
            ->connectTimeout(5)
            ->timeout(15)
            ->post(self::API_URL.'/v2/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [[
                            'currency' => 'PHP',
                            'amount' => (int) round((float) $payment->amount * 100),
                            'name' => 'Loan payment #'.$payment->id,
                            'quantity' => 1,
                        ]],
                        // Keep checkout on the method selected by the borrower.
                        // PayMongo displays a scannable GCash QR on desktop.
                        'payment_method_types' => $checkoutMethods,
                        'reference_number' => $payment->reference,
                        'success_url' => route('payments.success', ['payment' => $payment->id]),
                        'cancel_url' => route('payments.cancel', ['payment' => $payment->id]),
                        'description' => 'Payment for loan #'.$payment->loan_id,
                        'show_description' => true,
                        'show_line_items' => true,
                        'metadata' => [
                            'payment_id' => (string) $payment->id,
                            'loan_id' => (string) $payment->loan_id,
                        ],
                    ],
                ],
            ]);

        $response->throw();

        $attributes = $response->json('data.attributes');
        $checkoutUrl = $attributes['checkout_url'] ?? null;
        $reference = $response->json('data.id');

        $checkoutScheme = is_string($checkoutUrl) ? parse_url($checkoutUrl, PHP_URL_SCHEME) : null;
        $checkoutHost = is_string($checkoutUrl) ? parse_url($checkoutUrl, PHP_URL_HOST) : null;

        if (! $checkoutUrl || ! $reference
            || $checkoutScheme !== 'https' || $checkoutHost !== 'checkout.paymongo.com') {
            throw new RuntimeException('PayMongo returned an invalid checkout session.');
        }

        $payment->update([
            'provider' => 'paymongo',
            'provider_reference' => $reference,
            'paymongo_session_id' => $reference,
            'checkout_url' => $checkoutUrl,
        ]);

        return $checkoutUrl;
    }

    public function retrieveCheckoutSession(string $sessionId): array
    {
        $secretKey = config('paymongo.secret_key');

        if (! $secretKey) {
            throw new RuntimeException('PayMongo is not configured. Set PAYMONGO_SECRET_KEY first.');
        }

        return Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->connectTimeout(5)
            ->timeout(15)
            ->get(self::API_URL.'/v1/checkout_sessions/'.$sessionId)
            ->throw()
            ->json('data', []);
    }

    public function isValidWebhook(string $payload, ?string $signature): bool
    {
        $secret = config('paymongo.webhook_secret');

        if (! $secret || ! $signature) {
            return false;
        }

        $parts = collect(explode(',', $signature))->mapWithKeys(function (string $part) {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, null);

            return [trim($key) => trim((string) $value)];
        });

        $timestamp = $parts->get('t');
        $event = json_decode($payload, true);

        if (! is_array($event)) {
            return false;
        }

        $isLive = (bool) (data_get($event, 'data.attributes.livemode')
            ?? data_get($event, 'data.livemode', false));
        $provided = $isLive ? $parts->get('li') : $parts->get('te');

        if (! $timestamp || ! ctype_digit((string) $timestamp)
            || abs(now()->timestamp - (int) $timestamp) > config('paymongo.webhook_tolerance')) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        return (bool) $provided && hash_equals($expected, $provided);
    }
}
