<?php

return [
    'secret_key' => env('PAYMONGO_SECRET_KEY'),
    'public_key' => env('PAYMONGO_PUBLIC_KEY'),
    'webhook_secret' => env('PAYMONGO_WEBHOOK_SECRET'),
    'webhook_tolerance' => (int) env('PAYMONGO_WEBHOOK_TOLERANCE', 300),
    'gcash_checkout_method' => env('PAYMONGO_GCASH_METHOD', 'gcash'),
    'bank_transfer_checkout_methods' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('PAYMONGO_BANK_TRANSFER_METHODS', 'dob,brankas'))
    ))),
];
