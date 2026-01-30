<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "Testing CSV Import Logic...\n";

// Create a dummy CSV file
$csvContent = "full_name,email,password,phone,gender,member_type\n";
$csvContent .= "Import User 1,import1@example.com,pass123,111111,male,member\n";
$csvContent .= "Import User 2,import2@example.com,pass456,222222,female,pastor\n";

$csvFile = 'test_import.csv';
file_put_contents($csvFile, $csvContent);

try {
    $data = array_map('str_getcsv', file($csvFile));
    $header = array_shift($data);
    $headerMap = array_flip($header);

    foreach ($data as $row) {
        $email = $row[$headerMap['email']];
        echo "Processing $email...\n";

        // Create User
        $user = User::create([
            'name' => $row[$headerMap['full_name']],
            'email' => $email,
            'password' => Hash::make($row[$headerMap['password']]),
        ]);

        $user->assignRole($row[$headerMap['member_type']]);

        // Create Member
        $member = Member::create([
            'user_id' => $user->id,
            'full_name' => $row[$headerMap['full_name']],
            'email' => $email,
            'phone' => $row[$headerMap['phone']],
            'gender' => $row[$headerMap['gender']],
            'date_of_birth' => '1990-01-01',
            'marital_status' => 'single',
        ]);
        
        echo "[SUCCESS] Created User ID: {$user->id}, Role: {$row[$headerMap['member_type']]}\n";
        
        if (Hash::check($row[$headerMap['password']], $user->password)) {
            echo "[PASS] Password correct.\n";
        } else {
            echo "[FAIL] Password incorrect.\n";
        }
    }

} catch (\Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}

// Cleanup
unlink($csvFile);
