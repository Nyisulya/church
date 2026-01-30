<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use App\Models\User;

// Ensure a user exists
$user = User::first();
if (!$user) {
    $user = User::factory()->create();
}

// Simulate member creation
try {
    $member = new Member();
    $member->full_name = 'Test Member ' . uniqid();
    $member->email = 'test' . uniqid() . '@example.com';
    $member->gender = 'male';
    $member->date_of_birth = '1990-01-01';
    $member->marital_status = 'single';
    $member->user_id = $user->id;
    $member->save();

    echo "Member created successfully.\n";
    echo "Member Name: " . $member->full_name . "\n";
    echo "Member Number: " . $member->member_number . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
