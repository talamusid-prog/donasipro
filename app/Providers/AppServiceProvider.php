<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
<<<<<<< HEAD
use Illuminate\Support\Facades\URL;
=======
>>>>>>> ae8fb87a86c398ba07e3c9a086132b3c36b71066

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
<<<<<<< HEAD
        // Force HTTPS for all generated URLs when app URL is https
        $appUrl = config('app.url');
        if (is_string($appUrl) && str_starts_with($appUrl, 'https')) {
            URL::forceScheme('https');
        }
=======
        //
>>>>>>> ae8fb87a86c398ba07e3c9a086132b3c36b71066
    }
}
