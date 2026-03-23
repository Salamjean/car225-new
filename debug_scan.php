<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Reservation;
use App\Models\Voyage;

$ref = 'TX-WAL-EZFUXG79GL-2';
$res = Reservation::where('reference', $ref)->first();

if (!$res) {
    echo "Réservation non trouvée : $ref\n";
    exit;
}

echo "Réservation : " . $res->reference . "\n";
echo "Statut Global : [" . $res->statut . "]\n";
echo "Statut Aller : [" . $res->statut_aller . "]\n";
echo "Statut Retour : [" . $res->statut_retour . "]\n";
echo "Programme ID : " . $res->programme_id . "\n";
echo "Date Voyage : " . $res->date_voyage . "\n";

$voyage = Voyage::where('programme_id', $res->programme_id)
    ->whereDate('date_voyage', \Carbon\Carbon::parse($res->date_voyage))
    ->first();

if ($voyage) {
    echo "Voyage correspondant trouvé (ID: {$voyage->id})\n";
    echo "Occupancy via model : " . $voyage->occupancy . "\n";
} else {
    echo "Aucun voyage correspondant trouvé pour ce programme et cette date.\n";
}
