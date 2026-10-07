<?php

return [
    'secret_key' => env('PAYMONGO_SECRET_KEY'),
    'public_key' => env('PAYMONGO_PUBLIC_KEY'),
    'webhook_secret' => env('PAYMONGO_WEBHOOK_SECRET'),
    'webhook_tolerance' => (int) env('PAYMONGO_WEBHOOK_TOLERANCE', 300),
];
