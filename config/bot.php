<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | Token autentikasi untuk bot Telegram Anda, yang disimpan di file .env.
    | Pastikan Anda telah menambahkan "TELEGRAM_TOKEN" di file .env.
    |
    */
    'telegram_token' => env('TELEGRAM_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Default Bot Username
    |--------------------------------------------------------------------------
    |
    | Nama pengguna bot default yang dapat digunakan untuk referensi.
    | Ini opsional, tetapi membantu dalam pengelolaan beberapa bot.
    |
    */
    'default_bot_username' => env('TELEGRAM_BOT_USERNAME', 'MyTelegramBot'),

    /*
    |--------------------------------------------------------------------------
    | Telegram API Endpoint
    |--------------------------------------------------------------------------
    |
    | URL endpoint API Telegram. Anda dapat menyesuaikan ini jika diperlukan.
    | Secara default, ini menggunakan URL API Telegram resmi.
    |
    */
    'api_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org/bot'),

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | Jika diaktifkan, debug mode akan memungkinkan logging lebih mendetail
    | tentang aktivitas bot untuk tujuan pengembangan.
    |
    */
    'debug_mode' => env('TELEGRAM_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Webhook URL
    |--------------------------------------------------------------------------
    |
    | URL untuk mengatur webhook Telegram. Pastikan URL ini bisa diakses publik
    | jika Anda menggunakan webhook.
    |
    */
    'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
];
