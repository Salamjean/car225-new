<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Agent;
use App\Models\Caisse;
use App\Models\Personnel;
use App\Models\User;

function fillMissingCodes($modelClass) {
    $models = $modelClass::whereNull('code_id')->orWhere('code_id', '')->get();
    echo "Processing " . class_basename($modelClass) . ": " . $models->count() . " records found.\n";
    foreach($models as $model) {
        $model->code_id = $modelClass::generateUniqueCodeId();
        $model->save();
        echo "  - Generated code for ID {$model->id}: {$model->code_id}\n";
    }
}

echo "Filling missing code_ids...\n";
fillMissingCodes(Agent::class);
fillMissingCodes(Caisse::class);
fillMissingCodes(Personnel::class);
fillMissingCodes(User::class);
echo "Done.\n";
