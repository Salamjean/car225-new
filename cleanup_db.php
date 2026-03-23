<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Voyage;
use Carbon\Carbon;

$today = Carbon::today();

echo "--- CLEANUP RECAP ---\n";

// 1. Mark old un-finished voyages as finished
$oldVoyages = Voyage::whereNotIn('statut', ['terminé', 'annulé'])
    ->whereDate('date_voyage', '<', $today)
    ->get();

echo "Cleaning up " . $oldVoyages->count() . " old un-finished voyages...\n";
foreach ($oldVoyages as $v) {
    echo "  Voyage ID: {$v->id} (Date: {$v->date_voyage}) -> terminé\n";
    $v->update(['statut' => 'terminé']);
}

// 2. Reset Personnel status
$busyChauffeurs = Personnel::where('statut', '!=', 'disponible')->get();
foreach ($busyChauffeurs as $c) {
    $hasActiveVoyage = Voyage::where('personnel_id', $c->id)
        ->whereNotIn('statut', ['terminé', 'annulé'])
        ->exists();
    
    if (!$hasActiveVoyage) {
        echo "  Chauffeur '{$c->name} {$c->prenom}' (ID: {$c->id}) -> disponible\n";
        $c->update(['statut' => 'disponible']);
    }
}

// 3. Reset Vehicule status
$busyVehicules = Vehicule::where('statut', '!=', 'disponible')->get();
foreach ($busyVehicules as $v) {
    $hasActiveVoyage = Voyage::where('vehicule_id', $v->id)
        ->whereNotIn('statut', ['terminé', 'annulé'])
        ->exists();
    
    if (!$hasActiveVoyage) {
        echo "  Véhicule '{$v->immatriculation}' (ID: {$v->id}) -> disponible\n";
        $v->update(['statut' => 'disponible']);
    }
}

echo "Cleanup finished.\n";
