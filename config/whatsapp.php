<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp NodeJS Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk koneksi ke server NodeJS WhatsApp
    |
    */

    'node_url' => env('WHATSAPP_NODE_URL', 'http://localhost:3001'),
    
    'enabled' => env('WHATSAPP_NODE_ENABLED', false),
    
    'timeout' => env('WHATSAPP_TIMEOUT', 30),
    
    'retry_attempts' => env('WHATSAPP_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Method Configuration
    |--------------------------------------------------------------------------
    |
    | Pilih metode WhatsApp yang akan digunakan
    | Options: web_api, cloud_api, gateway_api, wa_blast_api
    |
    */

    'method' => env('WHATSAPP_DEFAULT_METHOD', 'wa_blast_api'),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Web API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk WhatsApp Web API (Manual QR Scan)
    |
    */

    'web_api' => [
        'node_url' => env('WHATSAPP_WEB_API_URL', 'http://localhost:3001'),
        'enabled' => env('WHATSAPP_WEB_API_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Cloud API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk WhatsApp Cloud API (Meta Official)
    |
    */

    'cloud_api' => [
        'token' => env('WHATSAPP_CLOUD_TOKEN'),
        'phone_id' => env('WHATSAPP_CLOUD_PHONE_ID'),
        'business_id' => env('WHATSAPP_CLOUD_BUSINESS_ID'),
        'verify_token' => env('WHATSAPP_CLOUD_VERIFY_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk WhatsApp Gateway API (Third Party)
    |
    */

    'gateway_api' => [
        'url' => env('WHATSAPP_GATEWAY_URL'),
        'token' => env('WHATSAPP_GATEWAY_TOKEN'),
        'instance' => env('WHATSAPP_GATEWAY_INSTANCE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WA Blast API Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk WA Blast API (Third Party Service)
    |
    */

    'wa_blast_api' => [
        // Jangan tambahkan /api/v1 di base_url, sudah otomatis di endpoint status
        'base_url' => env('WA_BLAST_API_BASE_URL', 'https://wa-blast.test'),
        'api_key' => env('WA_BLAST_API_KEY', 'wa-blast-lpFsrEK3wFuT0sUxzYmwafLvERwk2C8W'),
        'session_id' => env('WA_BLAST_SESSION_ID', 25),
        'session_uuid' => env('WA_BLAST_SESSION_UUID', '7d549d3d-a951-478e-b4d7-c90e465bd706'),
        'enabled' => env('WA_BLAST_ENABLED', true),
        'webhook_url' => env('WA_BLAST_WEBHOOK_URL'),
        'webhook_secret' => env('WA_BLAST_WEBHOOK_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Message Templates
    |--------------------------------------------------------------------------
    |
    | Template pesan untuk berbagai jenis notifikasi
    |
    */
    
    'templates' => [
        'donation_success' => [
            'title' => '🎉 Terima Kasih atas Donasi Anda!',
            'template' => "🎉 *Terima Kasih atas Donasi Anda!*\n\n" .
                         "📋 *Detail Donasi:*\n" .
                         "• ID: #{donation_id}\n" .
                         "• Kampanye: {campaign_title}\n" .
                         "• Jumlah: Rp {amount}\n" .
                         "• Status: {status}\n\n" .
                         "🙏 Semoga amal Anda diterima Allah SWT.\n" .
                         "💝 Jazakumullahu khairan."
        ],
        
        'donation_reminder' => [
            'title' => '⏰ Reminder Pembayaran Donasi',
            'template' => "⏰ *Reminder Pembayaran Donasi*\n\n" .
                         "📋 *Detail Donasi:*\n" .
                         "• ID: #{donation_id}\n" .
                         "• Kampanye: {campaign_title}\n" .
                         "• Jumlah: Rp {amount}\n" .
                         "• Batas Waktu: {expired_at}\n\n" .
                         "💳 Silakan selesaikan pembayaran Anda.\n" .
                         "🔗 {payment_url}"
        ],
        
        'payment_success' => [
            'title' => '✅ Pembayaran Berhasil',
            'template' => "✅ *Pembayaran Berhasil*\n\n" .
                         "📋 *Detail Pembayaran:*\n" .
                         "• ID: #{donation_id}\n" .
                         "• Kampanye: {campaign_title}\n" .
                         "• Jumlah: Rp {amount}\n" .
                         "• Metode: {payment_method}\n" .
                         "• Waktu: {paid_at}\n\n" .
                         "🙏 Terima kasih atas donasi Anda!"
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Notification Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan notifikasi WhatsApp
    |
    */

    'notifications' => [
        'donation_created' => true,
        'payment_success' => true,
        'payment_reminder' => true,
        'campaign_update' => false,
    ],

]; 