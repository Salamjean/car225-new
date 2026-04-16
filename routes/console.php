<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Planification du nettoyage des comptes désactivés
\Illuminate\Support\Facades\Schedule::job(new \App\Jobs\DeleteDeactivatedUsers)->daily();

// Nettoyage des réservations Wave abandonnées (sièges libérés automatiquement après 20 min sans paiement)
\Illuminate\Support\Facades\Schedule::command('reservations:clean-pending-wave --minutes=20')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/wave-cleanup.log'));

// Annulation automatique des convois confirmés non payés dont la date de départ est dépassée
\Illuminate\Support\Facades\Schedule::command('convoi:auto-annuler')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/convoi-auto-annuler.log'));
