<?php

return [
    'enabled' => (bool) env('MIDTRANS_ENABLED', false),
    'production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'snap_base_url' => env('MIDTRANS_SNAP_BASE_URL'),
    'api_base_url' => env('MIDTRANS_API_BASE_URL'),
    'timeout_seconds' => (int) env('MIDTRANS_TIMEOUT_SECONDS', 15),
];
