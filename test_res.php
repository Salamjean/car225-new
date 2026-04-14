<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$r = \App\Models\Reservation::find(92);
if ($r) {
    echo json_encode([
        'id' => $r->id,
        'passager_nom' => $r->passager_nom,
        'passager_prenom' => $r->passager_prenom,
        'passager_telephone' => $r->passager_telephone,
        'passager_urgence' => $r->passager_urgence,
        'nom_passager_urgence' => $r->nom_passager_urgence
    ], JSON_PRETTY_PRINT) . PHP_EOL;
} else {
    echo "Reservation non trouvée\n";
}

$r2 = \App\Models\Reservation::find(93);
if ($r2) {
    echo "Res 93:\n";
    echo json_encode([
        'id' => $r2->id,
        'passager_nom' => $r2->passager_nom,
        'passager_prenom' => $r2->passager_prenom,
        'passager_telephone' => $r2->passager_telephone,
        'passager_urgence' => $r2->passager_urgence,
        'nom_passager_urgence' => $r2->nom_passager_urgence
    ], JSON_PRETTY_PRINT) . PHP_EOL;
}
