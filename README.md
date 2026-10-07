# Inkcredible Loan Management System

Inkcredible is a Laravel 12 lending-management application for client onboarding, loan applications, repayment schedules, payment collection, account verification, and administrative reporting.

## Core features

- Client registration, email verification, profile management, and identity verification
- Configurable Arawan, weekly, and emergency loan products
- Loan review with automatic installment schedule creation
- Cash payment review and PayMongo-hosted QR Ph checkout
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
SEED_SUPERADMIN_EMAIL=superadmin@example.test
SEED_SUPERADMIN_PASSWORD=use-a-different-local-password
SEED_ADMIN_EMAIL=admin@example.test
SEED_ADMIN_PASSWORD=use-a-local-password
```

The seeders skip account creation when the corresponding email or password is missing. Never commit real or shared credentials.

## PayMongo configuration

Set the following values in `.env`:

```env
APP_URL=https://your-domain.example
PAYMONGO_SECRET_KEY=sk_test_your_key
PAYMONGO_WEBHOOK_SECRET=your_webhook_secret
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

## Gmail notifications

The application emails clients when their account is fully verified, a contract is ready to sign, a loan is approved, or an overdue penalty is applied. Dashboard notifications are also retained.

Create a Google App Password for the sending account, then set the mail values in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail-address@gmail.com
MAIL_PASSWORD=your-16-character-google-app-password
MAIL_FROM_ADDRESS=your-gmail-address@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

Do not commit the real App Password. After changing these values, run `php artisan config:clear`. Gmail App Passwords require 2-Step Verification on the Google account.

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

## Render with Clever Cloud MySQL

This repository includes `Dockerfile`, `docker/start.sh`, and `render.yaml` for
deployment as a Render Docker web service. Create a MySQL add-on in Clever
Cloud, then map its credentials to these Render environment variables:

```env
DB_CONNECTION=mysql
DB_HOST=<MYSQL_ADDON_HOST>
DB_PORT=<MYSQL_ADDON_PORT>
DB_DATABASE=<MYSQL_ADDON_DB>
DB_USERNAME=<MYSQL_ADDON_USER>
DB_PASSWORD=<MYSQL_ADDON_PASSWORD>
```

The Render Blueprint generates a stable `APP_KEY_SEED`; the startup script
derives a valid Laravel application key from it. It also derives `APP_URL` from
Render's external hostname. The container runs pending migrations on startup
and then caches Laravel configuration, routes, events, and views. On the first
deployment, provide `SEED_SUPERADMIN_EMAIL` and `SEED_SUPERADMIN_PASSWORD` so
the idempotent seeders create the initial administrator and loan products.
After the first successful deployment, set `RUN_SEEDERS=false` and remove both
seed credential variables from Render so a later deploy cannot reset the
administrator password.

Render's filesystem is ephemeral unless a persistent disk is attached. Use an
S3-compatible disk or a Render persistent disk before relying on uploaded
verification documents, profile photos, payment proofs, or signed contracts in
production.
