<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\\Contracts\\Console\\Kernel")->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $vehicles = DB::table("vehicules")->limit(10)->get();
    echo "=== Vehicle Configurations ===" . PHP_EOL;
    foreach ($vehicles as $vehicle) {
        echo json_encode($vehicle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
