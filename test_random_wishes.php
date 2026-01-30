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

echo "Sending 3 test wishes...\n";

$user->notify(new BirthdayGreetingNotification("Sender A"));
$user->notify(new BirthdayGreetingNotification("Sender B"));
$user->notify(new BirthdayGreetingNotification("Sender C"));

$notifications = $user->notifications()
    ->where('type', 'App\Notifications\BirthdayGreetingNotification')
    ->latest()
    ->take(3)
    ->get();

foreach ($notifications as $n) {
    echo "Message: " . $n->data['message'] . "\n";
}
