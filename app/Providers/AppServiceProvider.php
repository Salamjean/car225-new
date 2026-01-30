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
   public function boot()
{
    // Force HTTPS si on est en production OU si la requête vient de ngrok (via le header X-Forwarded-Proto)
    if($this->app->environment('production') || request()->header('X-Forwarded-Proto') === 'https') {
        URL::forceScheme('https');
    }
}
}
