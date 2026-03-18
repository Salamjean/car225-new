<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Voyage;
use Carbon\Carbon;

$today = Carbon::today();
$voyagesToday = Voyage::whereDate('date_voyage', $today)->with('programme')->get();

echo "Total voyages today in DB: " . $voyagesToday->count() . "\n";
foreach($voyagesToday as $v) {
    echo "- ID: {$v->id}, Programme ID: {$v->programme_id}, Date: {$v->date_voyage}\n";
    if ($v->programme) {
        $p = $v->programme;
        echo "  - Programme: From {$p->point_depart} To {$p->point_arrive}, Gare ID: {$p->gare_depart_id}\n";
    }
}
