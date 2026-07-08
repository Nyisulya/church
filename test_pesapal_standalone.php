<?php

// Standalone diagnostic script for Pesapal integration
// Does not load Laravel bootstrap to avoid permission issues with storage
// Run this via: php test_pesapal_standalone.php

echo "==================================================\n";
echo "PESAPAL STANDALONE DIAGNOSTIC TEST\n";
echo "==================================================\n\n";

// Parse .env manually
$envFile = __DIR__.'/.env';
if (!file_exists($envFile)) {
    echo "Error: .env file not found!\n";
    exit;
}

$envContent = file_get_contents($envFile);
$keys = [
    'PESAPAL_CONSUMER_KEY' => '',
    'PESAPAL_CONSUMER_SECRET' => '',
    'PESAPAL_ENV' => 'sandbox',
];

foreach ($keys as $key => $default) {
    if (preg_match('/^'.$key.'\s*=\s*["\']?([^"\']*)["\']?/m', $envContent, $matches)) {
        $keys[$key] = trim($matches[1]);
    }
}

$consumerKey = $keys['PESAPAL_CONSUMER_KEY'];
$consumerSecret = $keys['PESAPAL_CONSUMER_SECRET'];
$env = $keys['PESAPAL_ENV'];

echo "Consumer Key: " . substr($consumerKey, 0, 5) . "...\n";
echo "Consumer Secret: " . substr($consumerSecret, 0, 5) . "...\n";
echo "Env Mode: " . $env . "\n";

$baseUrl = ($env === 'production') ? 'https://pay.pesapal.com/v3/api' : 'https://cybqa.pesapal.com/pesapalv3/api';
echo "Target Base URL: " . $baseUrl . "\n\n";

// Helper for HTTP requests using cURL
function makePostRequest($url, $payload, $token = null) {
    $ch = curl_init($url);
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local testing safety
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['status' => $httpCode, 'body' => $response];
}

echo "1. Requesting Access Token...\n";
$tokenRes = makePostRequest($baseUrl . '/Auth/RequestToken', [
    'consumer_key' => $consumerKey,
    'consumer_secret' => $consumerSecret
]);

echo "HTTP Status: " . $tokenRes['status'] . "\n";
$tokenData = json_decode($tokenRes['body'], true);

if ($tokenRes['status'] === 200 && isset($tokenData['token'])) {
    $token = $tokenData['token'];
    echo "Token Result: SUCCESS\n";
} else {
    echo "Token Result: FAILED\n";
    echo "Response: " . $tokenRes['body'] . "\n";
    exit;
}

echo "\n2. Registering IPN URL...\n";
$ipnUrl = 'https://sdachurch.nyisu.com/pesapal/ipn'; 
echo "IPN URL to register: {$ipnUrl}\n";

$ipnRes = makePostRequest($baseUrl . '/URLSetup/RegisterIPN', [
    'url' => $ipnUrl,
    'ipn_notification_type' => 'GET'
], $token);

echo "HTTP Status: " . $ipnRes['status'] . "\n";
$ipnData = json_decode($ipnRes['body'], true);

if ($ipnRes['status'] === 200 && isset($ipnData['ipn_id'])) {
    $ipnId = $ipnData['ipn_id'];
    echo "IPN Result: SUCCESS (IPN ID: {$ipnId})\n";
} else {
    echo "IPN Result: FAILED\n";
    echo "Response: " . $ipnRes['body'] . "\n";
    exit;
}

echo "\n3. Submitting Test Order...\n";
$orderPayload = [
    'id' => 'TEST-' . time(),
    'amount' => 500.0,
    'description' => 'Test Payment via Standalone Tool',
    'callback_url' => 'https://sdachurch.nyisu.com/give/success',
    'notification_id' => $ipnId,
    'billing_address' => [
        'email_address' => 'test@example.com',
        'phone_number' => '255700000000',
        'country_code' => 'TZ',
        'first_name' => 'Test',
        'last_name' => 'User',
    ]
];

$orderRes = makePostRequest($baseUrl . '/Transactions/SubmitOrderRequest', $orderPayload, $token);
echo "HTTP Status: " . $orderRes['status'] . "\n";
echo "Response: " . $orderRes['body'] . "\n";
