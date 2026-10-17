<?php

return [
    'payment_deadline_hours' => (int) env('HARFEST_PAYMENT_DEADLINE_HOURS', 24),
    'max_qty_per_order' => (int) env('HARFEST_MAX_QTY_PER_ORDER', 10),
    'bank' => [
        'name' => env('HARFEST_BANK_NAME', 'BCA'),
        'account_number' => env('HARFEST_BANK_ACCOUNT', '1234567890'),
        'account_holder' => env('HARFEST_BANK_HOLDER', 'Panitia Harfest'),
    ],
    'qris_image' => 'images/qris-harfest.png',
    'contact_whatsapp' => env('HARFEST_CONTACT_WA', '6281234567890'),
];
