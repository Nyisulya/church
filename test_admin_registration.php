<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function testCreateMember($type, $roleName) {
    echo "Testing creation of $type...\n";
    
    $email = strtolower($type) . '_' . uniqid() . '@example.com';
    
    try {
        // 1. Create User
        $user = User::create([
            'name' => "$type User",
            'email' => $email,
            'password' => Hash::make('password123'),
        ]);
        
        // 2. Assign Role
        $user->assignRole($roleName);
        
        // 3. Create Member
        $member = Member::create([
            'user_id' => $user->id,
            'full_name' => "$type Member",
            'email' => $email,
            'gender' => 'male',
            'date_of_birth' => '1990-01-01',
            'marital_status' => 'single',
            'phone' => '1234567890',
        ]);
        
        echo "[SUCCESS] Created User ID: {$user->id}, Member ID: {$member->id}\n";
        echo "User Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
        
        if ($user->hasRole($roleName)) {
            echo "[PASS] Role '$roleName' assigned correctly.\n";
        } else {
            echo "[FAIL] Role '$roleName' NOT assigned.\n";
        }
        
        if ($member->user_id == $user->id) {
             echo "[PASS] Member linked to User correctly.\n";
        } else {
             echo "[FAIL] Member not linked to User.\n";
        }
        
    } catch (\Exception $e) {
        echo "[ERROR] " . $e->getMessage() . "\n";
    }
    echo "---------------------------------------------------\n";
}

testCreateMember('Pastor', 'pastor');
testCreateMember('Department Leader', 'department_leader');
testCreateMember('Regular Member', 'member');
