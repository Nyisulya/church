<?php

use App\Models\User;
use App\Notifications\BirthdayGreetingNotification;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = User::find(1); // Admin
if (!$user) die("User 1 not found");

echo "Checking wishes for {$user->name}...\n";

$wishes = $user->notifications()
    ->where('type', 'App\Notifications\BirthdayGreetingNotification')
    ->whereDate('created_at', today())
    ->get();

echo "Found " . $wishes->count() . " wishes from today.\n";

if ($wishes->isEmpty()) {
    echo "Creating a test wish...\n";
    $user->notify(new BirthdayGreetingNotification("Pastor John"));
    echo "Test wish created! Refresh your dashboard.\n";
} else {
    foreach ($wishes as $wish) {
        echo "- From: " . ($wish->data['sender'] ?? 'Unknown') . "\n";
    }
}
