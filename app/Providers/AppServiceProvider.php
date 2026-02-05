<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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
        // Fix for MySQL index length issue with utf8mb4
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
