<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Agent;

$agents = Agent::all(['id', 'name', 'prenom', 'code_id', 'gare_id']);
foreach ($agents as $agent) {
    echo "- ID: {$agent->id}, Name: {$agent->prenom} {$agent->name}, CodeID: {$agent->code_id}, GareID: {$agent->gare_id}\n";
}
