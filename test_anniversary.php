<?php

use App\Models\User;
use App\Models\Member;
use App\Notifications\AnniversaryGreetingNotification;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = User::find(1);
if (!$user) die("User 1 not found");

$member = $user->member;
if (!$member) die("Member for User 1 not found");

// Set marriage date to today to trigger the card
$member->marriage_date = Carbon::now()->subYears(5); // Married 5 years ago today
$member->save();

echo "Set marriage date to today for {$member->full_name}.\n";

// Clear old anniversary notifications
$user->notifications()
    ->where('type', 'App\Notifications\AnniversaryGreetingNotification')
    ->delete();

echo "Cleared old anniversary wishes.\n";
echo "Sending 3 NEW test anniversary wishes...\n";

$user->notify(new AnniversaryGreetingNotification("Pastor John"));
$user->notify(new AnniversaryGreetingNotification("Elder Mary"));
$user->notify(new AnniversaryGreetingNotification("Deacon James"));

echo "Done. Refresh dashboard to see the Anniversary Card!\n";
