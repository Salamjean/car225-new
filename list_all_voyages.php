<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Voyage;

$today = \Carbon\Carbon::today();
$voyages = Voyage::whereDate('date_voyage', $today)->with(['programme', 'chauffeur'])->get();

echo "Tous les voyages d'aujourd'hui :\n";
foreach ($voyages as $v) {
    $c = $v->chauffeur;
    $p = $v->programme;
    echo "  - Voyage ID: {$v->id}, Statut: {$v->statut}, Chauffeur: " . ($c ? "{$c->prenom} {$c->name}" : "Néant") . ", Programme ID: {$v->programme_id}\n";
    if ($p) {
        echo "    Ligne: {$p->point_depart} -> {$p->point_arrive} ({$p->heure_depart})\n";
    }
}
