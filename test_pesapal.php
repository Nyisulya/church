<?php

// Diagnostic script for Pesapal integration
// Run this via: php test_pesapal.php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Services\PesapalService;
use Illuminate\Support\Facades\Http;

$service = new PesapalService();
$env = config('services.pesapal.env', 'sandbox');
$baseUrl = ($env === 'production') ? 'https://pay.pesapal.com/v3/api' : 'https://cybqa.pesapal.com/pesapalv3/api';

echo "==================================================\n";
echo "PESAPAL DIAGNOSTIC TEST (Env: {$env})\n";
echo "Base URL: {$baseUrl}\n";
echo "==================================================\n\n";

echo "1. Requesting Access Token...\n";
$response = Http::post($baseUrl . '/Auth/RequestToken', [
    'consumer_key' => config('services.pesapal.consumer_key'),
    'consumer_secret' => config('services.pesapal.consumer_secret'),
]);

echo "HTTP Status: " . $response->status() . "\n";
if ($response->successful()) {
    $token = $response->json()['token'] ?? null;
    echo "Token Result: " . ($token ? "SUCCESS (Token retrieved)" : "FAILED (Token missing from response)") . "\n";
    if (!$token) {
        print_r($response->json());
        exit;
    }
} else {
    echo "Token Result: FAILED\n";
    echo "Error Body: " . $response->body() . "\n";
    exit;
}

echo "\n2. Registering IPN URL...\n";
$ipnUrl = route('pesapal.ipn');
echo "IPN URL to register: {$ipnUrl}\n";

$ipnResponse = Http::withToken($token)
    ->post($baseUrl . '/URLSetup/RegisterIPN', [
        'url' => $ipnUrl,
        'ipn_notification_type' => 'GET'
    ]);

echo "HTTP Status: " . $ipnResponse->status() . "\n";
if ($ipnResponse->successful()) {
    $ipnId = $ipnResponse->json()['ipn_id'] ?? null;
    echo "IPN Result: " . ($ipnId ? "SUCCESS (IPN ID: {$ipnId})" : "FAILED (IPN ID missing from response)") . "\n";
    if (!$ipnId) {
        print_r($ipnResponse->json());
        exit;
    }
} else {
    echo "IPN Result: FAILED\n";
    echo "Error Body: " . $ipnResponse->body() . "\n";
    exit;
}

echo "\n3. Submitting Test Order...\n";
$payload = [
    'id' => 'TEST-' . time(),
    'amount' => 500.0,
    'description' => 'Test Payment via Diagnostic Tool',
    'callback_url' => route('give.success'),
    'notification_id' => $ipnId,
    'billing_address' => [
        'email_address' => 'test@example.com',
        'phone_number' => '255700000000',
        'country_code' => 'TZ',
        'first_name' => 'Test',
        'last_name' => 'User',
    ]
];

$orderResponse = Http::withToken($token)
    ->post($baseUrl . '/Transactions/SubmitOrderRequest', $payload);

echo "HTTP Status: " . $orderResponse->status() . "\n";
if ($orderResponse->successful()) {
    $redirectUrl = $orderResponse->json()['redirect_url'] ?? null;
    echo "Order Result: " . ($redirectUrl ? "SUCCESS (Redirect URL: {$redirectUrl})" : "FAILED") . "\n";
    print_r($orderResponse->json());
} else {
    echo "Order Result: FAILED\n";
    echo "Error Body: " . $orderResponse->body() . "\n";
}
