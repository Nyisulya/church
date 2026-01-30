<?php

use App\Models\Member;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Current Date: " . now()->toDateTimeString() . "\n";

// 1. Find members with birthday today
$today = now();
$members = Member::whereMonth('date_of_birth', $today->month)
    ->whereDay('date_of_birth', $today->day)
    ->get();

echo "Found " . $members->count() . " members with birthday today.\n";

foreach ($members as $member) {
    echo "Member: {$member->full_name} (ID: {$member->id})\n";
    echo "DOB: {$member->date_of_birth->format('Y-m-d')}\n";
    echo "Has User Account: " . ($member->user ? "Yes (User ID: {$member->user->id})" : "No") . "\n";
    
    // Test Accessor
    echo "is_birthday_today accessor: " . ($member->is_birthday_today ? "TRUE" : "FALSE") . "\n";
    echo "-------------------\n";
}

// 2. Check if ANY member has DOB today (ignoring year)
if ($members->isEmpty()) {
    echo "No members found with birthday today (" . $today->format('M d') . ").\n";
    echo "To test the card, please update a member's DOB to: " . $today->format('Y') . "-12-06 (or any year).\n";
}
