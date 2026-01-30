<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

try {
    $user = User::first();
    echo "User: " . $user->name . "\n";
    
    // Test permissions relationship
    $permissions = $user->permissions;
    echo "Permissions count: " . $permissions->count() . "\n";
    
    // Test hasPermissionTo (if applicable)
    // $user->hasPermissionTo('edit articles'); 
    
    echo "Permissions relation works.\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
