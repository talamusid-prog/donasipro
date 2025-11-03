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
        // Force HTTPS for all generated URLs when app URL is https
        $appUrl = config('app.url');
        if (is_string($appUrl) && str_starts_with($appUrl, 'https')) {
            URL::forceScheme('https');
        }
    }
}
