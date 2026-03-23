<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Voyage;
use App\Models\Personnel;

$chauffeur = Personnel::where('prenom', 'like', '%WAYNE%')->orWhere('name', 'like', '%THE%')->first();

if (!$chauffeur) {
    echo "Chauffeur non trouvé.\n";
    exit;
}

echo "Chauffeur : " . $chauffeur->prenom . " " . $chauffeur->name . " (ID: " . $chauffeur->id . ")\n";
$today = \Carbon\Carbon::today();
$voyages = Voyage::where('personnel_id', $chauffeur->id)->whereDate('date_voyage', $today)->get();

echo "Voyages d'aujourd'hui :\n";
foreach ($voyages as $v) {
    echo "  - ID: {$v->id}, Statut: {$v->statut}, Programme ID: {$v->programme_id}\n";
    if ($v->programme) {
        echo "    Ligne: " . $v->programme->point_depart . " -> " . $v->programme->point_arrive . "\n";
    }
}
