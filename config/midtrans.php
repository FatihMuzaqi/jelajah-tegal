<?php

return [
    'enabled' => (bool) env('MIDTRANS_ENABLED', false),
    'production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', null),
    'server_key' => env('MIDTRANS_SERVER_KEY', null),
    'client_key' => env('MIDTRANS_CLIENT_KEY', null),
    'snap_base_url' => env('MIDTRANS_SNAP_BASE_URL', 'https://app.sandbox.midtrans.com'),
    'api_base_url' => env('MIDTRANS_API_BASE_URL', 'https://api.sandbox.midtrans.com'),
    'timeout_seconds' => (int) env('MIDTRANS_TIMEOUT_SECONDS', 15),
];
