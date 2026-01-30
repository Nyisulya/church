<?php

use App\Models\User;
use App\Notifications\BirthdayGreetingNotification;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = User::find(1);
if (!$user) die("User 1 not found");

// Delete existing birthday notifications from today to clean up
$user->notifications()
    ->where('type', 'App\Notifications\BirthdayGreetingNotification')
    ->whereDate('created_at', today())
    ->delete();

echo "Deleted old wishes.\n";

// Create a NEW test wish
echo "Creating a new test wish from 'Pastor John'...\n";
$user->notify(new BirthdayGreetingNotification("Pastor John"));

// Verify
$count = $user->notifications()
    ->where('type', 'App\Notifications\BirthdayGreetingNotification')
    ->whereDate('created_at', today())
    ->count();

echo "Now have {$count} wishes. Refresh dashboard!\n";
