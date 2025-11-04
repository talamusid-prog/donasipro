<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS jika di production atau jika request sudah menggunakan HTTPS
        // HTTPS detection akan dilakukan oleh TrustProxies middleware dan .htaccess
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        
        // Deteksi HTTPS dari proxy headers (jika ada)
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $_SERVER['HTTPS'] = 'on';
            URL::forceScheme('https');
        } elseif (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            URL::forceScheme('https');
        }
    }
}
