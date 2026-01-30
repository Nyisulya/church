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
     * Initiate a Mobile Money payment
     */
    public function initiateMobileMoney($amount, $currency, $phoneNumber, $email, $name, $network, $reference)
    {
        $endpoint = '/charges?type=mobile_money_tanzania'; // Default to TZ
        
        // Adjust endpoint/payload based on network if needed, but standard charge endpoint handles most
        // For specific mobile money, we might use specific endpoints or just the general charge
        
        $payload = [
            'amount' => $amount,
            'currency' => $currency,
            'phone_number' => $phoneNumber,
            'email' => $email,
            'fullname' => $name,
            'tx_ref' => $reference,
            'network' => $network, // VODACOM, AIRTEL, TIGO
        ];

        $response = Http::withToken($this->secretKey)
            ->post($this->baseUrl . $endpoint, $payload);

        return $response->json();
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction($transactionId)
    {
        $response = Http::withToken($this->secretKey)
            ->get($this->baseUrl . "/transactions/{$transactionId}/verify");

        return $response->json();
    }
}
