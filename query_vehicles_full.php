<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $vehicles = DB::table("vehicules")->select("id", "immatriculation", "nombre_place", "type_range", "statut")->limit(10)->get();
    echo "=== Vehicle Configurations ===" . PHP_EOL;
    echo str_pad("ID", 5) . str_pad("Immatriculation", 20) . str_pad("Nombre Places", 15) . str_pad("Type Range", 12) . str_pad("Statut", 15) . PHP_EOL;
    echo str_repeat("-", 77) . PHP_EOL;
    foreach ($vehicles as $vehicle) {
        echo str_pad($vehicle->id, 5) . str_pad($vehicle->immatriculation, 20) . str_pad($vehicle->nombre_place, 15) . str_pad($vehicle->type_range, 12) . str_pad($vehicle->statut, 15) . PHP_EOL;
    }
    echo PHP_EOL . "Total vehicles: " . count($vehicles) . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
