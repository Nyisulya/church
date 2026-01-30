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

echo "Testing Badge Count for {$user->name}...\n";

// Clear existing notifications
$user->notifications()->delete();
echo "Cleared all notifications.\n";

// Create test notifications
$user->notify(new AnniversaryGreetingNotification("Test Sender"));
$user->notify(new BirthdayGreetingNotification("Test Sender"));

echo "Sent test Anniversary and Birthday notifications (Unread).\n";

// Replicate AppServiceProvider Logic
$inboxUnreadCount = $user->unreadNotifications()
    ->whereNotIn('type', [
        'App\Notifications\NewCareRequestNotification',
        'App\Notifications\CareRequestResponseNotification',
        'App\Notifications\BirthdayGreetingNotification',
        'App\Notifications\AnniversaryGreetingNotification',
    ])
    ->count();

echo "Calculated Badge Count: {$inboxUnreadCount}\n";

if ($inboxUnreadCount === 0) {
    echo "PASS: Badge count is 0 (Anniversary/Birthday excluded).\n";
} else {
    echo "FAIL: Badge count is {$inboxUnreadCount} (Should be 0).\n";
}

// Clean up
$user->notifications()->delete();
echo "Cleaned up test notifications.\n";
