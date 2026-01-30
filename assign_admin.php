<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$user = User::find(1);
if ($user) {
    // Ensure role exists
    $role = Role::firstOrCreate(['name' => 'super_admin']);
    
    $user->assignRole('super_admin');
    echo "Assigned 'super_admin' role to user: " . $user->name . "\n";
} else {
    echo "User not found.\n";
}
