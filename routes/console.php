<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Planification du nettoyage des comptes désactivés
\Illuminate\Support\Facades\Schedule::job(new \App\Jobs\DeleteDeactivatedUsers)->daily();

