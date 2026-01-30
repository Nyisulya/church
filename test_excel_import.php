<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;

echo "Testing Excel/CSV Import Logic...\n";

// Create a dummy CSV file (Maatwebsite handles CSVs too)
$csvContent = "name,email,password\n";
$csvContent .= "Excel User 1,excel1@example.com,pass123\n";
$csvContent .= "Excel User 2,excel2@example.com,pass456\n";

$csvFile = 'test_excel_import.csv';
file_put_contents($csvFile, $csvContent);

try {
    // Simulate UploadedFile
    $uploadedFile = new UploadedFile($csvFile, 'test_excel_import.csv', 'text/csv', null, true);

    // Use Maatwebsite Excel to read the file
    echo "Reading file using Maatwebsite\\Excel...\n";
    $array = Excel::toArray([], $uploadedFile);

    if (empty($array) || empty($array[0])) {
        throw new \Exception('The file is empty or could not be read.');
    }

    $data = $array[0];
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
