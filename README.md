# Inkcredible Loan System

## Optional local accounts

To create development accounts with `php artisan db:seed`, set `SEED_ADMIN_EMAIL` and `SEED_ADMIN_PASSWORD` in your local `.env`. Set `SEED_CLIENT_EMAIL` and `SEED_CLIENT_PASSWORD` if you also want a demo client. Both seeders skip account creation when their email or password is missing. Do not use shared or predictable passwords on a deployed site.

## PayMongo setup

The payment page supports GCash and Maya through PayMongo Checkout. Cash payments remain manual and are reviewed by an administrator.

1. Point your domain DNS records to the server running this Laravel application.
2. Enable HTTPS for the domain.
3. Set these values in `.env`:

```env
APP_URL=https://your-domain.example
PAYMONGO_SECRET_KEY=sk_test_your_key
PAYMONGO_WEBHOOK_SECRET=your_webhook_secret
PAYMONGO_GCASH_METHOD=gcash
```

If the PayMongo test account has QR Ph enabled but not the separate GCash capability, set `PAYMONGO_GCASH_METHOD=qrph`. The checkout will generate a bill-specific QR that can be scanned using GCash.

4. Run `php artisan migrate` and clear cached configuration with `php artisan config:clear`.
5. In the PayMongo dashboard, add this webhook endpoint:

```text
https://your-domain.example/api/paymongo/webhook
```

Subscribe to `checkout_session.payment.paid`. The signed webhook is the main source of truth. The success-return page also performs an authenticated server-side status check with PayMongo so a completed payment can be reflected immediately if the webhook is delayed.

Use PayMongo test keys while testing, then replace them with live keys after verification.

### Local development with ngrok

Start Laravel in one terminal:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Start ngrok in another terminal:

```bash
ngrok http 8000
```

Copy the generated HTTPS URL, for example `https://example.ngrok-free.app`, into `.env`:

```env
APP_URL=https://example.ngrok-free.app
```

Register `https://example.ngrok-free.app/api/paymongo/webhook` in the PayMongo **test-mode** dashboard under **Developers > Webhooks**. Copy the secret shown for that endpoint into `PAYMONGO_WEBHOOK_SECRET`. If the ngrok URL changes, update both `APP_URL` and the PayMongo endpoint.

### Test procedure

1. Run `php artisan migrate` and `php artisan config:clear`.
2. Use a `sk_test_...` key and a test-mode webhook endpoint. Never put the secret key in Blade, JavaScript, or committed files.
3. Log in as a borrower with an approved loan, open `/payments`, verify the amount due, choose GCash or Maya, and select **Pay Now**.
4. Complete the payment on PayMongo's hosted test checkout. The app verifies the Checkout Session on return and also processes the signed webhook; it never trusts a browser redirect by itself.
5. Confirm the webhook delivery is successful in the PayMongo dashboard and inspect `storage/logs/laravel.log` for unmatched or amount-mismatch warnings.
6. Verify the payment row has `status=paid`, `paymongo_session_id`, `paymongo_payment_id`, `paid_at`, and the provider method. Verify `loans.paid_amount` and the remaining balance changed.
7. Retry the same webhook from the PayMongo dashboard. The unique event ID must prevent a second balance update.
8. Use PayMongo's failed test case and confirm the payment becomes `failed` without changing the loan balance. Refund a successful test payment and confirm it becomes `refunded` and the loan balance is recalculated.

The webhook accepts only a valid `Paymongo-Signature` header, rejects stale requests outside `PAYMONGO_WEBHOOK_TOLERANCE`, and records each event ID once. PayMongo webhook delivery must receive a 2xx response within 30 seconds.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
