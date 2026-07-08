<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PesapalService
{
    protected $baseUrl;
    protected $consumerKey;
    protected $consumerSecret;
    protected $ipnId;

    public function __construct()
    {
        $this->consumerKey = config('services.pesapal.consumer_key');
        $this->consumerSecret = config('services.pesapal.consumer_secret');
        $this->ipnId = config('services.pesapal.ipn_id');
        
        $env = config('services.pesapal.env', 'sandbox');
        $this->baseUrl = ($env === 'production') 
            ? 'https://pay.pesapal.com/v3/api' 
            : 'https://cybqa.pesapal.com/pesapalv3/api';
    }

    /**
     * Get the Authorization Token (with caching for performance)
     */
    public function getAccessToken()
    {
        return Cache::remember('pesapal_access_token', 240, function () { // Cache for 4 minutes (expires in 5m)
            $response = Http::post($this->baseUrl . '/Auth/RequestToken', [
                'consumer_key' => $this->consumerKey,
                'consumer_secret' => $this->consumerSecret,
            ]);

            if ($response->successful()) {
                return $response->json()['token'] ?? null;
            }

            Log::error('Pesapal Authentication Failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return null;
        });
    }

    /**
     * Register IPN URL dynamically and return IPN ID
     */
    public function getOrRegisterIPN($token)
    {
        if ($this->ipnId) {
            return $this->ipnId;
        }

        // Try to register dynamically
        $ipnUrl = route('pesapal.ipn');
        
        $response = Http::withToken($token)
            ->post($this->baseUrl . '/URLSetup/RegisterIPN', [
                'url' => $ipnUrl,
                'ipn_notification_type' => 'GET'
            ]);

        if ($response->successful()) {
            $ipnId = $response->json()['ipn_id'] ?? null;
            if ($ipnId) {
                Log::info('Pesapal IPN Registered dynamically', ['ipn_id' => $ipnId, 'url' => $ipnUrl]);
                return $ipnId;
            }
        }

        Log::error('Pesapal IPN Registration Failed', [
            'status' => $response->status(),
            'response' => $response->body(),
            'url' => $ipnUrl
        ]);
        
        return null;
    }

    /**
     * Submit payment order and return redirect URL
     */
    public function submitOrder($amount, $reference, $description, $email, $name, $phone)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        $ipnId = $this->getOrRegisterIPN($token);
        if (!$ipnId) {
            Log::error('Pesapal Submit Order failed because IPN ID is null');
            return null;
        }

        // Build names
        $names = explode(' ', trim($name));
        $firstName = $names[0] ?? 'Mshiriki';
        $lastName = $names[1] ?? 'Kanisa';

        $payload = [
            'id' => $reference,
            'amount' => (float)$amount,
            'description' => $description,
            'callback_url' => route('give.success'),
            'notification_id' => $ipnId,
            'billing_address' => [
                'email_address' => $email,
                'phone_number' => $phone,
                'country_code' => 'TZ',
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]
        ];

        $response = Http::withToken($token)
            ->post($this->baseUrl . '/Transactions/SubmitOrderRequest', $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Pesapal Submit Order Request Failed', [
            'status' => $response->status(),
            'response' => $response->body(),
            'payload' => $payload
        ]);

        return null;
    }

    /**
     * Get Transaction Status
     */
    public function getTransactionStatus($trackingId)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        $response = Http::withToken($token)
            ->get($this->baseUrl . "/Transactions/GetTransactionStatus?orderTrackingId={$trackingId}");

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Pesapal Get Transaction Status Failed', [
            'status' => $response->status(),
            'response' => $response->body(),
            'tracking_id' => $trackingId
        ]);

        return null;
    }
}
