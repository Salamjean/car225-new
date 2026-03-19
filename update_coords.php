<?php

use App\Models\Gare;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$villes = [
    'Abidjan' => [5.348441, -4.030500],
    'Bouaké' => [7.691111, -5.029722],
    'Daloa' => [6.877308, -6.450223],
    'San-Pédro' => [4.748511, -6.636300],
    'Yamoussoukro' => [6.820556, -5.276667],
    'Alépé' => [5.498800, -3.666700],
    'Korhogo' => [9.458028, -5.629528],
    'Bassam' => [5.2094, -3.7431], // Grand-Bassam
    'Grand-Bassam' => [5.2094, -3.7431], 
];

$updated = 0;
foreach ($villes as $ville => $coords) {
    $gares = Gare::where('ville', 'like', "%$ville%")->get();
    foreach ($gares as $gare) {
        if (!$gare->latitude) {
            $gare->update([
                'latitude' => $coords[0],
                'longitude' => $coords[1],
            ]);
            $updated++;
            echo "Gare '{$gare->nom_gare}' ({$gare->ville}) mise à jour avec lat: {$coords[0]}, lon: {$coords[1]}\n";
        }
    }
}

echo "\nTotal gares mises à jour : $updated\n";
