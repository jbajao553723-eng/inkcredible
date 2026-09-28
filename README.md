# Inkcredible Loan Management System

Inkcredible is a Laravel 12 lending-management application for client onboarding, loan applications, repayment schedules, payment collection, account verification, and administrative reporting.

## Core features

- Client registration, email verification, profile management, and identity verification
- Configurable Arawan, weekly, and emergency loan products
- Loan review with automatic installment schedule creation
- Cash payment review and PayMongo-hosted GCash, Maya, and bank-transfer checkout
- Signed, idempotent PayMongo webhook processing and payment reconciliation
- Client and business PDF reports
- Responsive client and administrator workspaces

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL or another database supported by Laravel
- The PHP extensions required by Laravel and DOMPDF

## Local setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

On Windows PowerShell systems that block `npm.ps1`, use `cmd /c npm run build`.

The application is then available at `http://127.0.0.1:8000`.

## Development accounts

Seed accounts are optional. Add these values to `.env` before running `php artisan db:seed`:

```env
SEED_ADMIN_EMAIL=admin@example.test
SEED_ADMIN_PASSWORD=use-a-local-password
SEED_CLIENT_EMAIL=client@example.test
SEED_CLIENT_PASSWORD=use-a-local-password
```

The seeders skip account creation when the corresponding email or password is missing. Never commit real or shared credentials.

## PayMongo configuration

Set the following values in `.env`:

```env
APP_URL=https://your-domain.example
PAYMONGO_SECRET_KEY=sk_test_your_key
PAYMONGO_WEBHOOK_SECRET=your_webhook_secret
PAYMONGO_GCASH_METHOD=gcash
PAYMONGO_BANK_TRANSFER_METHODS=dob,brankas
```

Register this webhook URL in the PayMongo dashboard and subscribe to `checkout_session.payment.paid`:

```text
https://your-domain.example/api/paymongo/webhook
```

The signed webhook is the primary source of truth. The success-return page also performs an authenticated server-side reconciliation when possible. Use test credentials during development and clear cached configuration after changing environment values:

```bash
php artisan config:clear
```

For local webhook testing, expose the app through an HTTPS tunnel and update both `APP_URL` and the PayMongo webhook endpoint to the tunnel URL.

## Quality checks

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

## Project structure

```text
app/Http/Controllers   HTTP orchestration only
app/Http/Requests      Authorization and input validation
app/Models             Eloquent models, relationships, and domain helpers
app/Services           Loan, payment, provider, and reporting workflows
resources/views        Blade pages, partials, and reusable components
resources/js           Shared UI behavior and administrator tools
tests/Feature          End-to-end application behavior
```

Uploaded verification documents use private storage. Public profile images, payment proofs, and loan documents are stored on the public disk; run `php artisan storage:link` in environments that need public file delivery.

## Production notes

- Serve the application over HTTPS.
- Configure a queue worker and scheduler for production workloads.
- Keep `APP_DEBUG=false` and use unique production credentials.
- Cache configuration, routes, and views during deployment with `php artisan optimize`.
- Back up both the database and uploaded files.
