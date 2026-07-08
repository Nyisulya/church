<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveService
{
    protected $baseUrl = 'https://api.flutterwave.com/v3';
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.flutterwave.secret_key');
    }

    /**
     * Initiate a Hosted Checkout payment URL
     */
    public function initiateCheckout($amount, $currency, $email, $name, $phone, $reference, $redirectUrl, $description)
    {
        $endpoint = '/payments';
        
        $payload = [
            'tx_ref' => $reference,
            'amount' => $amount,
            'currency' => $currency,
            'redirect_url' => $redirectUrl,
            'customer' => [
                'email' => $email,
                'phonenumber' => $phone,
                'name' => $name,
            ],
            'customizations' => [
                'title' => 'Manzese SDA Church Online Giving',
                'description' => $description,
            ]
        ];

        $response = Http::withToken($this->secretKey)
            ->post($this->baseUrl . $endpoint, $payload);

        return $response->json();
    }

    /**
     * Verify a transaction status
     */
    public function verifyTransaction($transactionId)
    {
        $response = Http::withToken($this->secretKey)
            ->get($this->baseUrl . "/transactions/{$transactionId}/verify");

        return $response->json();
    }
}
