<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;
use App\Helpers\Token;
use Illuminate\Support\Facades\DB;

class GoogleController extends Controller
{
    public $auth;
    public function __construct()
    {
        $this->auth = auth()->guard('api');
    }

    /**
     * Redirect to Google for authentication.
     */
    public function redirectToGoogle()
    {
        // Your logic to redirect to Google's OAuth service
    }

    /**
     * Handle the callback from Google.
     */
    // Method to handle user login with Google
    public function authenticateWithGoogle(Request $request)
    {
        $accessToken = $request->query('accessToken');

        if (!$accessToken) {
            return response()->json(['error' => 'Access token not provided'], 400);
        }

        $googleApiUrl = config('services.google.userinfo_url');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get($googleApiUrl);

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to authenticate with Google'], 500);
        }

        $userInfo = $response->json();
        $email = $userInfo['email'] ?? null;

        if (!$email) {
            return response()->json(['error' => 'Email not retrieved from Google account'], 500);
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->email_verified = true;
            $user->save();
            JWTAuth::factory()->setTTL(120);
            $token = JWTAuth::fromUser($user);


            return response()->json([
                'user' => $user,
                'access_token' => $token,
            ]);
        } else {
            // No user found, register a new one
            DB::beginTransaction();
            try {
                $user = new User;
                $user->email = $email;
                $user->password = Hash::make('somePassword');
                $user->name = $userInfo['name'] ?? 'Google User';
                $user->is_verified = true;
                $user->save();

                $role = Role::firstOrCreate(['name' => 'user']);
                $user->assignRole($role);

                if ($user) {
                    $identifier = $user->id . '_type-Reg';
                    $token = new Token($identifier);
                    $otpResponse = $token->getToken();
                    $responseArray = $otpResponse->getData(true);
                    $otpCode = $responseArray['token']['token'];

                    $token->addOTPVerification($user->id, $otpCode);

                    // Mail::to($user->email)->send(new OTPMailer($user, $otpCode));
                }

                JWTAuth::factory()->setTTL(20);
                $tokenUser = JWTAuth::fromUser($user);

                DB::commit();
                return response()->json([
                    'user' => $user,
                    'access_token' => $tokenUser,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Registration Exception: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to register the user' . $e->getMessage()], 500);
            }
        }
    }

    function UserNumberExists($number)
    {
        return User::whereUserId($number)->exists();
    }

}
