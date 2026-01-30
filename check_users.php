<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Checking users in database:\n";
echo str_repeat('-', 50) . "\n";

$users = User::all(['id', 'email', 'name']);
echo "Total users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Email: {$user->email}\n";
    echo "Name: {$user->name}\n";
    echo "Has password: " . (strlen($user->password ?? '') > 0 ? 'Yes' : 'No') . "\n";
    echo str_repeat('-', 50) . "\n";
}
