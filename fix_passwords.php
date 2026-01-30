<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "Updating user passwords directly...\n";
echo str_repeat('-', 50) . "\n";

$users = [
    'admin@church.com',
    'pastor@church.com',
    'treasurer@church.com',
    'member@church.com',
];

foreach ($users as $email) {
    $hashedPassword = Hash::make('password');
    
    DB::table('users')
        ->where('email', $email)
        ->update(['password' => $hashedPassword]);
    
    echo "✓ Updated password for: $email\n";
}

echo "\nVerifying passwords...\n";
echo str_repeat('-', 50) . "\n";

foreach ($users as $email) {
    $user = User::where('email', $email)->first();
    if ($user && $user->password) {
        echo "✓ $email has password set (length: " . strlen($user->password) . ")\n";
    } else {
        echo "✗ $email - NO PASSWORD!\n";
    }
}

echo "\nDone!\n";
