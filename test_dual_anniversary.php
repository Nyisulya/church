<?php

use App\Models\User;
use App\Models\Member;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = User::find(1);
$member = $user->member;

echo "Testing Dual Anniversary Columns for {$member->full_name}...\n";

// Test 1: Only wedding_date set
$member->marriage_date = null;
$member->wedding_date = Carbon::now()->subYears(10);
$member->save();

echo "Set wedding_date to 10 years ago, marriage_date to NULL.\n";
echo "Is Anniversary Today? " . ($member->is_anniversary_today ? 'YES' : 'NO') . "\n";
echo "Anniversary Date: " . ($member->anniversary_date ? $member->anniversary_date->format('Y-m-d') : 'NULL') . "\n";

// Test Controller Logic (Simulated)
$month = now()->month;
$query = Member::where(function($q) use ($month) {
    $q->whereMonth('marriage_date', $month)
      ->orWhereMonth('wedding_date', $month);
})->where('id', $member->id);

echo "Found in Query? " . ($query->exists() ? 'YES' : 'NO') . "\n";

// Test 2: Only marriage_date set
$member->marriage_date = Carbon::now()->subYears(5);
$member->wedding_date = null;
$member->save();

echo "\nSet marriage_date to 5 years ago, wedding_date to NULL.\n";
echo "Is Anniversary Today? " . ($member->is_anniversary_today ? 'YES' : 'NO') . "\n";
echo "Anniversary Date: " . ($member->anniversary_date ? $member->anniversary_date->format('Y-m-d') : 'NULL') . "\n";

$query = Member::where(function($q) use ($month) {
    $q->whereMonth('marriage_date', $month)
      ->orWhereMonth('wedding_date', $month);
})->where('id', $member->id);

echo "Found in Query? " . ($query->exists() ? 'YES' : 'NO') . "\n";
