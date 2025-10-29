<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppSettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register helper function
        if (!function_exists('app_setting')) {
            function app_setting($key, $default = null) {
                return AppSetting::getValue($key, $default);
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share app settings to all views
        View::composer('*', function ($view) {
            $appSettings = AppSetting::getAllAsArray();
            $view->with('appSettings', $appSettings);
            
            // Generate custom CSS for primary color
            $primaryColor = $appSettings['primary_color'] ?? '#2563eb';
            $customCSS = "
                :root {
                    --primary-color: {$primaryColor};
                }
                
                .bg-primary {
                    background-color: {$primaryColor} !important;
                }
                
                .text-primary {
                    color: {$primaryColor} !important;
                }
                
                .border-primary {
                    border-color: {$primaryColor} !important;
                }
                
                .focus\\:ring-primary:focus {
                    --tw-ring-color: {$primaryColor} !important;
                }
                
                .focus\\:border-primary:focus {
                    border-color: {$primaryColor} !important;
                }
                
                .hover\\:bg-primary:hover {
                    background-color: {$primaryColor} !important;
                }
                
                .hover\\:text-primary:hover {
                    color: {$primaryColor} !important;
                }
                
                .hover\\:border-primary:hover {
                    border-color: {$primaryColor} !important;
                }
                
                /* Brand Blue Classes */
                .text-brand-blue {
                    color: {$primaryColor} !important;
                }
                
                .bg-brand-blue {
                    background-color: {$primaryColor} !important;
                }
                
                .border-brand-blue {
                    border-color: {$primaryColor} !important;
                }
                
                .hover\\:text-brand-blue:hover {
                    color: {$primaryColor} !important;
                }
                
                .hover\\:bg-brand-blue:hover {
                    background-color: {$primaryColor} !important;
                }
                
                .hover\\:border-brand-blue:hover {
                    border-color: {$primaryColor} !important;
                }
                
                .focus\\:border-brand-blue:focus {
                    border-color: {$primaryColor} !important;
                }
                
                .focus\\:ring-brand-blue:focus {
                    --tw-ring-color: {$primaryColor} !important;
                }
            ";
            
            $view->with('customCSS', $customCSS);
        });
    }
}
