<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Programme;
use Carbon\Carbon;

$today = Carbon::today();
$allToday = Programme::whereDate('date_depart', $today)->with('gareDepart')->get();

echo "Total programmes today in DB: " . $allToday->count() . "\n";
foreach($allToday as $p) {
    echo "- ID: {$p->id}, Gare: " . ($p->gareDepart->nom_gare ?? 'N/A') . " ({$p->gare_depart_id}), From: {$p->point_depart}\n";
}
