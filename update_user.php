<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::where('email', 'test@example.com')->first();
if ($user) {
    $user->password = Hash::make('password123');
    $user->save();
    
    // Assign super_admin role
    $user->assignRole('super_admin');
    
    echo "User password updated to: password123\n";
    echo "User email: " . $user->email . "\n";
    echo "User roles: " . $user->getRoleNames()->implode(', ') . "\n";
} else {
    echo "User not found\n";
}
