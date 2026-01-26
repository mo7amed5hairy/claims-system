<?php
// Debug script to check database consistency for Returned Invoice validation
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Modules\Claims\Models\Claim;
use App\Modules\Claims\Models\ClaimEntity;

echo "--- Searching for 'وزارة المالية' ---\n";
$finance = ClaimEntity::where('name', 'like', '%المالية%')->first();
if ($finance) {
    echo "Found: ID: {$finance->id} | Name: {$finance->name}\n";
} else {
    echo "Entity 'وزارة المالية' not found!\n";
}

echo "\n--- Searching for Claim with Invoice '456' ---\n";
$claim = Claim::where('electronic_invoice_no', '456')->first();
if ($claim) {
    echo "Found Claim ID: {$claim->id} | Invoice: {$claim->electronic_invoice_no} | Entity ID: {$claim->entity_id}\n";
    $targetEntity = ClaimEntity::find($claim->entity_id);
    if ($targetEntity) {
        echo "This claim belongs to: ID: {$targetEntity->id} | Name: {$targetEntity->name}\n";
    }
} else {
    echo "Claim with invoice '456' not found!\n";
}
