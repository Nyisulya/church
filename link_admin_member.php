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
if (!$user) {
    die("User ID 1 not found.\n");
}

if ($user->member) {
    echo "User already has a member profile: {$user->member->full_name}\n";
    $user->member->date_of_birth = now();
    $user->member->save();
    echo "Updated DOB to today.\n";
} else {
    echo "Creating member profile for Admin...\n";
    $member = Member::create([
        'user_id' => $user->id,
        'full_name' => $user->name,
        'email' => $user->email,
        'phone' => '0000000000',
        'gender' => 'male',
        'marital_status' => 'single',
        'address' => 'Church HQ',
        'date_of_birth' => now(), // Birthday is TODAY
        'status' => 'active',
        'salvation_date' => now(),
        'baptism_date' => now(),
        'emergency_contact_name' => 'God',
        'emergency_contact_phone' => '777',
    ]);
    echo "Created member profile for {$member->full_name} with Birthday TODAY!\n";
}
