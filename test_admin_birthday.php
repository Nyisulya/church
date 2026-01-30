<?php

use App\Models\User;
use App\Models\Member;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = User::find(1);
if ($user) {
    echo "User ID 1: {$user->name}\n";
    if ($user->member) {
        echo "Linked Member: {$user->member->full_name}\n";
        echo "DOB: " . ($user->member->date_of_birth ? $user->member->date_of_birth->format('Y-m-d') : "NULL") . "\n";
        echo "Is Birthday Today? " . ($user->member->is_birthday_today ? "YES" : "NO") . "\n";
    } else {
        echo "No linked member profile.\n";
    }
} else {
    echo "User ID 1 not found.\n";
}
