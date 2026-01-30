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

// Clear old notifications to avoid confusion with old format
$user->notifications()
    ->where('type', 'App\Notifications\BirthdayGreetingNotification')
    ->delete();

echo "Cleared old wishes.\n";
echo "Sending 3 NEW test wishes...\n";

$user->notify(new BirthdayGreetingNotification("Pastor John"));
$user->notify(new BirthdayGreetingNotification("Elder Mary"));
$user->notify(new BirthdayGreetingNotification("Deacon James"));

echo "Done. Refresh dashboard to see dynamic translations.\n";
