<?php

use App\Models\User;
use App\Notifications\AnniversaryGreetingNotification;
use App\Notifications\BirthdayGreetingNotification;
use Illuminate\Support\Facades\Auth;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = User::find(1);
Auth::login($user);

echo "Testing Inbox Filtering for {$user->name}...\n";

// Create test notifications
$user->notify(new AnniversaryGreetingNotification("Test Sender"));
$user->notify(new BirthdayGreetingNotification("Test Sender"));

echo "Sent test Anniversary and Birthday notifications.\n";

// Replicate InboxController query
$notifications = Auth::user()->notifications()
    ->whereNotIn('type', [
        \App\Notifications\NewCareRequestNotification::class,
        \App\Notifications\CareRequestResponseNotification::class,
        \App\Notifications\BirthdayGreetingNotification::class,
        \App\Notifications\AnniversaryGreetingNotification::class,
    ])
    ->get();

$hasAnniversary = $notifications->contains(function ($notification) {
    return $notification->type === \App\Notifications\AnniversaryGreetingNotification::class;
});

$hasBirthday = $notifications->contains(function ($notification) {
    return $notification->type === \App\Notifications\BirthdayGreetingNotification::class;
});

echo "Inbox contains Anniversary Notification? " . ($hasAnniversary ? 'YES (FAIL)' : 'NO (PASS)') . "\n";
echo "Inbox contains Birthday Notification? " . ($hasBirthday ? 'YES (FAIL)' : 'NO (PASS)') . "\n";

// Clean up
$user->notifications()
    ->whereIn('type', [
        \App\Notifications\AnniversaryGreetingNotification::class,
        \App\Notifications\BirthdayGreetingNotification::class
    ])
    ->delete();
echo "Cleaned up test notifications.\n";
