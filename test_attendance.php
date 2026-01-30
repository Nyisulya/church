<?php

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Starting Attendance Test ---\n";

// 1. Create Event
$event = Event::create([
    'name' => 'Test Service ' . time(),
    'type' => 'service',
    'date' => Carbon::today(),
    'start_time' => '09:00:00',
]);
echo "Event created: {$event->name} (ID: {$event->id})\n";

// 2. Get Member
$member = Member::first();
if (!$member) {
    echo "No members found. Creating one...\n";
    $member = Member::create([
        'first_name' => 'Test',
        'last_name' => 'Member',
        'email' => 'test' . time() . '@example.com',
        'phone' => '1234567890',
        'status' => 'active',
    ]);
}
echo "Member found: {$member->full_name} (ID: {$member->id})\n";

// 3. Check QR Content
$qrContent = $member->qr_code_content;
echo "QR Content: $qrContent\n";

// 4. Record Attendance
try {
    $attendance = Attendance::create([
        'event_id' => $event->id,
        'member_id' => $member->id,
        'scanned_at' => now(),
        'status' => 'present',
    ]);
    echo "Attendance recorded successfully (ID: {$attendance->id})\n";
} catch (\Exception $e) {
    echo "Error recording attendance: " . $e->getMessage() . "\n";
}

// 5. Test Duplicate
try {
    Attendance::create([
        'event_id' => $event->id,
        'member_id' => $member->id,
        'scanned_at' => now(),
        'status' => 'present',
    ]);
    echo "Duplicate attendance recorded (UNEXPECTED)\n";
} catch (\Illuminate\Database\QueryException $e) {
    echo "Duplicate attendance prevented (EXPECTED)\n";
}

echo "--- Test Finished ---\n";
