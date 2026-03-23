<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Programme;

$ids = [3, 9];
foreach ($ids as $id) {
    $p = Programme::find($id);
    if ($p) {
        echo "Programme ID: $id\n";
        echo "  - Départ: {$p->point_depart}\n";
        echo "  - Arrivée: {$p->point_arrive}\n";
        echo "  - Heure: {$p->heure_depart}\n";
        echo "  - Compagnie ID: {$p->compagnie_id}\n";
    }
}
