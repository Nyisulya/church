<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Mock Auth
$admin = User::where('email', 'admin@church.com')->first();
Auth::login($admin);

echo "Testing Member Creation with Custom Password...\n";

$email = 'password_test_' . uniqid() . '@example.com';
$password = 'SecretPass123!';

try {
    // Simulate Request Data
    $data = [
        'full_name' => 'Password Test User',
        'email' => $email,
        'password' => $password,
        'gender' => 'male',
        'date_of_birth' => '1990-01-01',
        'marital_status' => 'single',
        'member_type' => 'member',
    ];

    // 1. Create User (Simulating Controller Logic)
    $user = User::create([
        'name' => $data['full_name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);
    
    $user->assignRole($data['member_type']);
    
    // 2. Create Member
    $member = Member::create([
        'user_id' => $user->id,
        'full_name' => $data['full_name'],
        'email' => $data['email'],
        'gender' => $data['gender'],
        'date_of_birth' => $data['date_of_birth'],
        'marital_status' => $data['marital_status'],
    ]);

    echo "[SUCCESS] Created User ID: {$user->id}\n";
    
    // Verify Password
    if (Hash::check($password, $user->password)) {
        echo "[PASS] Password matches input.\n";
    } else {
        echo "[FAIL] Password does NOT match input.\n";
    }
    
    // Verify Default Password (should NOT match)
    if (!Hash::check('password123', $user->password)) {
        echo "[PASS] Password is NOT default 'password123'.\n";
    } else {
        echo "[FAIL] Password IS default 'password123'.\n";
    }

} catch (\Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}
