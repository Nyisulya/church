<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::with('roles')->get();

foreach ($users as $user) {
    echo "User: " . $user->name . " (ID: " . $user->id . ")\n";
    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
    echo "Has 'super_admin' or 'admin': " . ($user->hasAnyRole(['super_admin', 'admin']) ? 'YES' : 'NO') . "\n";
    echo "-----------------------------------\n";
}
