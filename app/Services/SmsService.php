<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS using the SimpApp Android SMS Gateway API
     *
     * @param string $phone
     * @param string $message
     * @return bool
     */
    public static function send($phone, $message)
    {
        $apiKey = env('SMS_GATEWAY_API_KEY');

        if (!$apiKey) {
            Log::error('SMS Gateway: API Key not set in .env');
            return false;
        }

        // Format phone number to E.164 (+255...)
        $formattedPhone = self::formatPhoneNumber($phone);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://europe-west1-sms-gateway-api-simpapp.cloudfunctions.net/api_v2_sms_send', [
                'phoneNumber' => $formattedPhone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("SMS sent successfully via Gateway to {$formattedPhone}: {$message}");
                return true;
            }

            Log::error("SMS Gateway Failed sending to {$formattedPhone}. Status: " . $response->status() . " Response: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("SMS Gateway Exception sending to {$formattedPhone}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Standardize phone numbers to Tanzanian E.164 format (+255...)
     */
    public static function formatPhoneNumber($phone)
    {
        // Remove spaces, dashes, brackets, and any non-numeric/non-plus characters
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // If it already starts with a plus, return it as is
        if (str_starts_with($phone, '+')) {
            return $phone;
        }
        
        // If it starts with 0, replace with +255
        if (str_starts_with($phone, '0')) {
            return '+255' . substr($phone, 1);
        }
        
        // If it starts with 255, prepend +
        if (str_starts_with($phone, '255')) {
            return '+' . $phone;
        }
        
        // Default fallback: assume Tanzanian number and prepend +255
        return '+255' . $phone;
    }
}
