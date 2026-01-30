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
    // Force HTTPS si on est en production OU si l'URL contient ngrok
    if($this->app->environment('production') || str_contains(config('app.url'), 'ngrok')) {
        URL::forceScheme('https');
    }
}
}
