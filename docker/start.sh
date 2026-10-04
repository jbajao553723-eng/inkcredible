#!/bin/sh
set -eu

if [ -z "${APP_KEY:-}" ] && [ -n "${APP_KEY_SEED:-}" ]; then
    APP_KEY="base64:$(php -r 'echo base64_encode(hash("sha256", getenv("APP_KEY_SEED"), true));')"
    export APP_KEY
fi

if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY or APP_KEY_SEED must be configured." >&2
    exit 1
fi

if [ -z "${APP_URL:-}" ] && [ -n "${RENDER_EXTERNAL_HOSTNAME:-}" ]; then
    APP_URL="https://${RENDER_EXTERNAL_HOSTNAME}"
    export APP_URL
fi

mkdir -p \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

php artisan package:discover --ansi
php artisan storage:link || true

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

php artisan optimize

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
