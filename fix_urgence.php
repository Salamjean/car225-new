<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$reservations = \App\Models\Reservation::whereNotNull('passager_urgence')
    ->where('passager_urgence', 'like', '%(%')
    ->where('passager_urgence', 'like', '%)%')
    ->get();

$count = 0;
foreach ($reservations as $r) {
    if (preg_match('/^(.*?)\s*\((.*?)\)$/', $r->passager_urgence, $matches)) {
        $nom = trim($matches[1]);
        $telephone = trim($matches[2]);
        
        // Update only if nom_passager_urgence is empty
        if (empty($r->nom_passager_urgence)) {
            $r->nom_passager_urgence = $nom;
            $r->passager_urgence = $telephone;
            $r->save();
            $count++;
            echo "Fixed ID {$r->id}: Nom = {$nom}, Tel = {$telephone}\n";
        }
    }
}
echo "Total fixed: {$count}\n";
