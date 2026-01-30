<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

try {
    // Create a test permission
    $permission = Permission::firstOrCreate(['name' => 'test-permission']);
    echo "Permission created/found: " . $permission->name . "\n";

    // Create a test role
    $role = Role::firstOrCreate(['name' => 'test-role']);
    echo "Role created/found: " . $role->name . "\n";

    // Assign permission to role
    $role->syncPermissions(['test-permission']);
    echo "Permission assigned to role.\n";

    // Check if role has permission
    if ($role->hasPermissionTo('test-permission')) {
        echo "Role has permission.\n";
    } else {
        echo "Role does NOT have permission.\n";
    }

    // Clean up
    $role->delete();
    $permission->delete();
    echo "Cleaned up.\n";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
