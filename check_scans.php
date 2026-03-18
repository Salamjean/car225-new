<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Programme;
use App\Models\Agent;
use App\Models\Reservation;
use Carbon\Carbon;

$today = Carbon::today();
echo "Current Carbon::today(): " . $today->toDateString() . "\n";

$agent = Agent::where('code_id', 'AGT-745704')->first();
if ($agent) {
    echo "Agent Name: {$agent->name}, Gare ID: {$agent->gare_id}\n";
    
    $scansToday = Reservation::where('embarquement_agent_id', $agent->id)
        ->whereDate('embarquement_scanned_at', $today)
        ->count();
    echo "Scans today (DB count): $scansToday\n";

    // If scans exist, look at the programme they belong to
    $recentScans = Reservation::where('embarquement_agent_id', $agent->id)
        ->whereDate('embarquement_scanned_at', $today)
        ->with('programme')
        ->get();
    
    foreach ($recentScans as $scan) {
        $p = $scan->programme;
        echo "- Scan ID: {$scan->id}, Ref: {$scan->reference}, ScannedAt: {$scan->embarquement_scanned_at}\n";
        if ($p) {
            echo "  - Programme ID: {$p->id}, Date Depart: " . ($p->date_depart ? $p->date_depart->toDateString() : 'NULL') . ", Gare Depart: {$p->gare_depart_id}\n";
        } else {
            echo "  - NO PROGRAMME LINKED\n";
        }
    }

    // Check programmes for this gare today
    $pCount = Programme::where('gare_depart_id', $agent->gare_id)
        ->whereDate('date_depart', $today)
        ->count();
    echo "Programmes for this gare today: $pCount\n";

    // Check if there are programmes for TOMORROW (2026-03-19)
    $tomorrow = $today->copy()->addDay();
    echo "Checking tomorrow: " . $tomorrow->toDateString() . "\n";
    $pCountT = Programme::where('gare_depart_id', $agent->gare_id)
        ->whereDate('date_depart', $tomorrow)
        ->count();
    echo "Programmes for this gare tomorrow: $pCountT\n";

} else {
    echo "Agent 'AGT-745704' not found.\n";
}
