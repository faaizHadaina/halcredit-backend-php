<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected $baseUrl;
    protected $secretKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.paystack.co';
        $this->secretKey = env('PAYSTACK_SECRET_KEY');
    }

    public function initializePayment(array $data)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
        ])->post("{$this->baseUrl}/transaction/initialize", $data);

        return $response->json();
    }

    public function bankAccountVerification(string $bankCode, string $account_number)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
        ])->get("{$this->baseUrl}/bank/resolve?account_number={$account_number}&bank_code={$bankCode}");
            
        return $response->json();
    }

    public function fetchAllBanks()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
        ])->get("{$this->baseUrl}/bank");
            
        return $response->json();
    }

    public function verifyPayment(string $reference)
    {
        try {
            $encodedReference = urlencode($reference);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-cache',
            ])->get("{$this->baseUrl}/transaction/verify/{$encodedReference}");

            if ($response->successful()) {
                $responseBody = $response->json();
                Log::info('Verify Payment Response', ['response' => $responseBody]);
                return $responseBody;
            } else {
                Log::error('Verify Payment Error', ['response' => $response->body()]);
                return ['status' => 'error', 'message' => 'Failed to verify payment'];
            }
        } catch (\Exception $e) {
            Log::error('Exception in Verify Payment', ['exception' => $e->getMessage()]);
            return ['status' => 'error', 'message' => 'An exception occurred while verifying payment'];
        }
    }

}
