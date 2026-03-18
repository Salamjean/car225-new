<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Agent;

$agents = Agent::all(['id', 'name', 'prenom', 'code_id', 'gare_id']);
$output = "";
foreach ($agents as $agent) {
    $output .= "- ID: {$agent->id}, Name: {$agent->prenom} {$agent->name}, CodeID: {$agent->code_id}, GareID: {$agent->gare_id}\n";
}
file_put_contents('agents_list.txt', $output);
