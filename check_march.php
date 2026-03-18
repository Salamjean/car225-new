<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Programme;

$pMarch = Programme::whereMonth('date_depart', 3)->whereYear('date_depart', 2026)->get(['id', 'gare_depart_id', 'date_depart', 'point_depart']);

$output = "Programmes in March 2026:\n";
foreach($pMarch as $p) {
    $output .= "- ID: {$p->id}, Gare: {$p->gare_depart_id}, Date: {$p->date_depart->toDateString()}, From: {$p->point_depart}\n";
}

file_put_contents('march_programmes.txt', $output);
