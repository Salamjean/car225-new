<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Programme;
use App\Models\Agent;
use Carbon\Carbon;

$today = Carbon::today();
echo "Current Carbon::today(): " . $today->toDateString() . "\n";
echo "Current now(): " . now()->toDateTimeString() . "\n";

$programmesCount = Programme::whereDate('date_depart', $today)->count();
echo "Programmes count for today: " . $programmesCount . "\n";

$allProgrammes = Programme::take(5)->get(['id', 'date_depart', 'point_depart', 'gare_depart_id']);
echo "Sample programmes:\n";
foreach ($allProgrammes as $p) {
    echo "- ID: {$p->id}, Date: " . ($p->date_depart ? $p->date_depart->toDateString() : 'NULL') . ", From: {$p->point_depart}, Gare ID: {$p->gare_depart_id}\n";
}

$agent = Agent::find(1); // Assuming ID 1 exists or adjust
if ($agent) {
    echo "Agent ID 1 Gare ID: {$agent->gare_id}\n";
    $pForAgent = Programme::where('gare_depart_id', $agent->gare_id)
        ->whereDate('date_depart', $today)
        ->count();
    echo "Programmes for Agent 1 Gare today: $pForAgent\n";
}
