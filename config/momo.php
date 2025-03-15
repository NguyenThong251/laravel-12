<?php

return [
    'partner_code' => env('MOMO_PARTNER_CODE', 'YOUR_PARTNER_CODE'),
    'access_key' => env('MOMO_ACCESS_KEY', 'YOUR_ACCESS_KEY'),
    'secret_key' => env('MOMO_SECRET_KEY', 'YOUR_SECRET_KEY'),
    'url' => env('MOMO_URL', 'https://test-payment.momo.vn/v2/gateway/api/create'),
    'return_url' => env('MOMO_RETURN_URL', 'http://localhost:8000/api/payments/momo/callback'),
    'notify_url' => env('MOMO_NOTIFY_URL', 'http://localhost:8000/api/payments/momo/notify'),
];
