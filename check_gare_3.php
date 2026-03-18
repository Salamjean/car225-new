<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Programme;
use Carbon\Carbon;

$today = Carbon::today();
$tomorrow = $today->copy()->addDay();

$pToday = Programme::where('gare_depart_id', 3)->whereDate('date_depart', $today)->get(['id', 'point_depart', 'point_arrive', 'date_depart']);
$pTomorrow = Programme::where('gare_depart_id', 3)->whereDate('date_depart', $tomorrow)->get(['id', 'point_depart', 'point_arrive', 'date_depart']);
$pAll = Programme::where('gare_depart_id', 3)->take(10)->get(['id', 'point_depart', 'point_arrive', 'date_depart']);

$output = "Checking Gare ID 3 (Gare d'Alepe?)\n";
$output .= "Today: " . $today->toDateString() . "\n";
$output .= "Tomorrow: " . $tomorrow->toDateString() . "\n";

$output .= "Programmes for today count: " . count($pToday) . "\n";
foreach($pToday as $p) {
    $output .= "- ID: {$p->id}, From: {$p->point_depart}, To: {$p->point_arrive}, Date: {$p->date_depart->toDateString()}\n";
}

$output .= "Programmes for tomorrow count: " . count($pTomorrow) . "\n";
foreach($pTomorrow as $p) {
    $output .= "- ID: {$p->id}, From: {$p->point_depart}, To: {$p->point_arrive}, Date: {$p->date_depart->toDateString()}\n";
}

$output .= "Sample programmes for this gare:\n";
foreach($pAll as $p) {
    $output .= "- ID: {$p->id}, From: {$p->point_depart}, To: {$p->point_arrive}, Date: " . ($p->date_depart ? $p->date_depart->toDateString() : 'NULL') . "\n";
}

file_put_contents('gare_3_programmes.txt', $output);
