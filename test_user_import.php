<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "Testing User-Only CSV Import Logic...\n";

// Create a dummy CSV file
$csvContent = "name,email,password,confirm_password\n";
$csvContent .= "User Only 1,useronly1@example.com,pass123,pass123\n";
$csvContent .= "User Only 2,useronly2@example.com,pass456,pass456\n";

$csvFile = 'test_user_import.csv';
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
            'name' => $row[$headerMap['name']],
            'email' => $email,
            'password' => Hash::make($row[$headerMap['password']]),
        ]);

        $user->assignRole('member');

        echo "[SUCCESS] Created User ID: {$user->id}\n";
        
        // Verify NO Member record exists
        $member = Member::where('user_id', $user->id)->first();
        if (!$member) {
            echo "[PASS] No Member record created.\n";
        } else {
            echo "[FAIL] Member record WAS created.\n";
        }

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
