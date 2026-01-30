<?php

use App\Models\Announcement;
use App\Models\Department;
use App\Models\Member;
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Starting Department Test ---\n";

// 1. Create Department
$department = Department::firstOrCreate([
    'name' => 'Test Choir',
    'description' => 'Singing for the Lord',
]);
echo "Department created/found: {$department->name} (ID: {$department->id})\n";

// 2. Assign Member
$member = Member::first();
if ($member) {
    if (!$department->members->contains($member->id)) {
        $department->members()->attach($member->id, ['role' => 'member']);
        echo "Member assigned to department.\n";
    } else {
        echo "Member already in department.\n";
    }
}

// 3. Create Announcement
$user = User::first();
if ($user) {
    $announcement = Announcement::create([
        'department_id' => $department->id,
        'user_id' => $user->id,
        'title' => 'Rehearsal on Friday',
        'body' => 'Please come prepared.',
    ]);
    echo "Announcement created: {$announcement->title} (ID: {$announcement->id})\n";
} else {
    echo "No user found to create announcement.\n";
}

// 4. Verify Relationships
$deptAnnouncements = $department->announcements;
echo "Department has {$deptAnnouncements->count()} announcements.\n";

echo "--- Test Finished ---\n";
