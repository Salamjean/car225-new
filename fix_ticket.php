<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Reservation;

$ref = 'TX-WAL-EZFUXG79GL-2';
$res = Reservation::where('reference', $ref)->first();

if ($res) {
    echo "Mise à jour du billet $ref vers le voyage ID 35...\n";
    $res->update(['voyage_id' => 35]);
    echo "Fait !\n";
} else {
    echo "Billet non trouvé.\n";
}
