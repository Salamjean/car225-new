<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Personnel;
use App\Models\Vehicule;
use App\Models\Voyage;

echo "--- CHAUFFEURS INDISPONIBLES ---\n";
$chauffeurs = Personnel::where('type_personnel', 'Chauffeur')->where('statut', '!=', 'disponible')->get();
foreach ($chauffeurs as $c) {
    echo "ID: {$c->id}, Nom: {$c->name} {$c->prenom}, Statut: {$c->statut}\n";
    $activeVoyages = Voyage::where('personnel_id', $c->id)->whereNotIn('statut', ['terminé', 'annulé'])->get();
    echo "  Voyages actifs: " . $activeVoyages->count() . "\n";
    foreach ($activeVoyages as $v) {
        echo "    Voyage ID: {$v->id}, Date: {$v->date_voyage}, Statut: {$v->statut}\n";
    }
}

echo "\n--- VEHICULES INDISPONIBLES ---\n";
$vehicules = Vehicule::where('statut', '!=', 'disponible')->get();
foreach ($vehicules as $v) {
    echo "ID: {$v->id}, Immat: {$v->immatriculation}, Statut: {$v->statut}\n";
    $activeVoyages = Voyage::where('vehicule_id', $v->id)->whereNotIn('statut', ['terminé', 'annulé'])->get();
    echo "  Voyages actifs: " . $activeVoyages->count() . "\n";
    foreach ($activeVoyages as $voyage) {
        echo "    Voyage ID: {$voyage->id}, Date: {$voyage->date_voyage}, Statut: {$voyage->statut}\n";
    }
}
