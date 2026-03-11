<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Agent;
use App\Models\Caisse;
use App\Models\Personnel;

echo "Creating Agent...\n";
try {
    $agent = new Agent([
        'name' => 'Test',
        'prenom' => 'Agent',
        'email' => 'test_agent_'.time().'@test.com',
        'contact' => '0102030405',
        'commune' => 'Test',
        'cas_urgence' => '0504030201',
        'password' => 'password',
        'compagnie_id' => 1, // Add these to avoid DB error
        'gare_id' => 1
    ]);
    $agent->save();
    echo "Agent Code ID: " . ($agent->code_id ?? 'NULL') . "\n";
} catch (\Exception $e) {
    echo "Error Creating Agent: " . $e->getMessage() . "\n";
}

echo "Creating Caisse...\n";
try {
    $caisse = Caisse::create([
        'name' => 'Test',
        'prenom' => 'Caisse',
        'email' => 'test_caisse_'.time().'@test.com',
        'contact' => '0102030405',
        'password' => 'password',
        'compagnie_id' => 1,
        'gare_id' => 1
    ]);
    echo "Caisse Code ID: " . ($caisse->code_id ?? 'NULL') . "\n";
} catch (\Exception $e) {
    echo "Error Creating Caisse: " . $e->getMessage() . "\n";
}

echo "Creating Personnel...\n";
try {
    $personnel = Personnel::create([
        'name' => 'Test',
        'prenom' => 'Chauffeur',
        'type_personnel' => 'Chauffeur',
        'email' => 'test_chauffeur_'.time().'@test.com',
        'contact' => '0102030405',
        'contact_urgence' => '0504030201',
        'password' => 'password',
        'statut' => 'indisponible',
        'compagnie_id' => 1,
        'gare_id' => 1
    ]);
    echo "Personnel Code ID: " . ($personnel->code_id ?? 'NULL') . "\n";
} catch (\Exception $e) {
    echo "Error Creating Personnel: " . $e->getMessage() . "\n";
}
