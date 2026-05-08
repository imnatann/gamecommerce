<?php

return [

    'payment' => [
        'default_gateway' => env('PAYMENT_GATEWAY', 'midtrans'),

        'midtrans' => [
            'server_key' => env('MIDTRANS_SERVER_KEY'),
            'client_key' => env('MIDTRANS_CLIENT_KEY'),
            'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
            'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
            'is_3ds' => env('MIDTRANS_IS_3DS', true),
            'append_notif_url' => env('MIDTRANS_APPEND_NOTIF_URL', ''),
            'override_notif_url' => env('MIDTRANS_OVERRIDE_NOTIF_URL', ''),
        ],

        'xendit' => [
            'secret_key' => env('XENDIT_SECRET_KEY'),
            'public_key' => env('XENDIT_PUBLIC_KEY'),
            'webhook_secret' => env('XENDIT_WEBHOOK_SECRET'),
            'is_production' => env('XENDIT_IS_PRODUCTION', false),
        ],

        'methods' => [
            'qris',
            'bank_transfer',
            'ewallet',
            'credit_card',
            'convenience_store',
        ],

        'ewallet_providers' => ['gopay', 'ovo', 'dana', 'shopeepay', 'linkaja'],
        'bank_codes' => ['bca', 'bni', 'bri', 'mandiri', 'permata', 'cimb'],
        'convenience_stores' => ['alfamart', 'indomaret'],
    ],

    'products' => [
        'per_page' => env('GAMECOMMERCE_PRODUCTS_PER_PAGE', 24),
        'max_upload_size' => env('GAMECOMMERCE_MAX_UPLOAD_SIZE', 5120),
        'allowed_image_mimes' => ['image/jpeg', 'image/png', 'image/webp'],
        'max_images_per_product' => 5,
        'types' => ['topup', 'game_key', 'item', 'account', 'voucher', 'joki', 'coin'],
    ],

    'commission' => [
        'default_rate' => env('GAMECOMMERCE_COMMISSION_RATE', 5),
        'premium_seller_rate' => env('GAMECOMMERCE_PREMIUM_COMMISSION_RATE', 3),
        'boosting_service_rate' => env('GAMECOMMERCE_BOOSTING_COMMISSION_RATE', 8),
        'min_withdrawal' => env('GAMECOMMERCE_MIN_WITHDRAWAL', 50000),
    ],

    'orders' => [
        'expiry_minutes' => env('GAMECOMMERCE_ORDER_EXPIRY_MINUTES', 30),
        'auto_complete_days' => env('GAMECOMMERCE_AUTO_COMPLETE_DAYS', 3),
        'dispute_window_hours' => env('GAMECOMMERCE_DISPUTE_WINDOW_HOURS', 24),
    ],

    'regions' => [
        'supported' => ['ID', 'SEA', 'GLOBAL'],
        'default' => 'ID',
    ],

    'kyc' => [
        'required_for_seller' => true,
        'max_document_size' => 5120,
        'allowed_document_mimes' => ['image/jpeg', 'image/png', 'application/pdf'],
    ],

    'cache' => [
        'ttl_games' => env('GAMECOMMERCE_CACHE_TTL_GAMES', 3600),
        'ttl_products' => env('GAMECOMMERCE_CACHE_TTL_PRODUCTS', 1800),
        'ttl_categories' => env('GAMECOMMERCE_CACHE_TTL_CATEGORIES', 7200),
        'ttl_banners' => env('GAMECOMMERCE_CACHE_TTL_BANNERS', 1800),
        'ttl_search' => env('GAMECOMMERCE_CACHE_TTL_SEARCH', 900),
    ],

    'meilisearch' => [
        'index_prefix' => env('MEILISEARCH_INDEX_PREFIX', 'gamecommerce_'),
    ],

    'storage' => [
        'disk' => env('GAMECOMMERCE_STORAGE_DISK', 'local'),
    ],

];