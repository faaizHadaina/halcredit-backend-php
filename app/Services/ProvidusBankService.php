<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Str;
use Illuminate\Http\Client\Response as HttpClientResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ProvidusBankService
{
    private $apiUrl;
    private $clientId;
    private $clientSecret;
    private $testUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.providus.api_url');
        $this->clientId = config('services.providus.client_id');
        $this->clientSecret = config('services.providus.client_secret');
        $this->testUrl = config('services.providus.test_url');
    }

    private function generateXAuthSignature(): string
    {
        $concatenatedString = $this->clientId . ':' . $this->clientSecret;
        $sha512Hash = hash('sha512', $concatenatedString);

        Log::info('X-Auth-Signature: ' . $sha512Hash);

        return $sha512Hash;
    }

    private function makeHttpRequest(string $url, array $payload): HttpClientResponse
    {
        $xAuthSignature = $this->generateXAuthSignature();

        $headers = [
            'X-Auth-Signature' => $xAuthSignature,
            'Client-Id' => $this->clientId,
        ];

        return Http::withHeaders($headers)
            ->post($url, $payload);
    }

    public function createBankAccount($userID)
    {
        try {
            $user = User::findOrFail($userID);
            $profile = Profile::where('user_id', $user->id)->firstOrFail();

            if (is_null($profile->BVN)) {
                return response()->json(['message' => 'BVN is not available for this account. Please update BVN details.'], 400);
            }

            $existingWallet = Wallet::where('user_id', $userID)->where('walletStatus', 1)->first();
            if ($existingWallet) {
                return response()->json(['message' => 'User already has an active account.'], 400);
            }

            $payload = [
                'account_name' => $user->name,
                'bvn' => $profile->BVN
            ];
            $url = "{$this->apiUrl}/PiPCreateReservedAccountNumber";

            $apiResponse = $this->makeHttpRequest($url, $payload)->throw()->json();
            Log::info('API Response: ' . json_encode($apiResponse));

            if (!$apiResponse['requestSuccessful']) {
                return response()->json(['message' => 'Account generation api error, please contact support for resolutions'], 400);
            }

            $newAccountName = $apiResponse['account_name'];
            $newAccountNumber = $apiResponse['account_number'];

            $inactiveWallet = Wallet::where('user_id', $userID)->where('walletStatus', 0)->first();

            if (!$inactiveWallet) {
                $wallet = Wallet::create([
                    'user_id' => $userID,
                    'currency' => 'NGR',
                    'bank_name' => 'Providus Bank',
                    'account_name' => $newAccountName,
                    'account_number' => $newAccountNumber,
                    'wallet_balance' => 0,
                    'status' => 1,
                    'transaction_pin' => null,
                ]);
            } else {
                $inactiveWallet->update([
                    'accountName' => $newAccountName,
                    'accountNumber' => $newAccountNumber,
                    'walletStatus' => 1,
                ]);
                $wallet = $inactiveWallet;
            }

            return response()->json(['wallet' => $wallet], 200);
        } catch (\Exception $e) {
            Log::info('Error in createBankAccount: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function pushSettlement($settlement_id, $session_id)
    {
        try {

            $payload = [
                'settlement_id' => $settlement_id,
                'session_id' => $session_id
            ];
            $url = "{$this->apiUrl}/PiP_RepushTransaction_SettlementId";

            $apiResponse = $this->makeHttpRequest($url, $payload)->throw()->json();
            Log::info('API Response: ' . json_encode($apiResponse));

            return response()->json(['data' => $apiResponse], 200);
        } catch (\Throwable $e) {
            Log::error('Error in pushing settlement: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to push settlement.'], 500);
        }
    }

    public function updateBankAccountName(string $accountNumber, string $accountName, int $userID): array
    {
        try {
            $payload = [
                'account_number' => $accountNumber,
                'account_name' => $accountName
            ];
            $url = "{$this->apiUrl}/PiPUpdateAccountName";

            $wallet = Wallet::findOrFail($userID);
            $wallet->update(['accountName' => $accountName]);

            $apiResponse = $this->makeHttpRequest($url, $payload)->throw()->json();
            return $apiResponse;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}

//Account generation encountered a mismatch error, please contact support for resolutions
