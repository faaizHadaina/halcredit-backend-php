<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\MyController;
use App\Mail\OTPMailer;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Helpers\Token;
use carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends MyController
{
    //Constructor to load model using repository design pattern
    protected $user;
    private $verificationUrl;
    protected $token;
    public $auth;
    public function __construct() {
        $this->auth = auth()->guard('api');
        $this->verificationUrl = config('services.auth.verification_url');
    }
    /**
     * register new user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $dataU = [
                'email' => $request->email,
                'name' => $request->name
            ];

            $request->merge($dataU);

            $user = $this->user->create($request);

            $role = Role::firstOrCreate(['name' => "User", 'guard_name' => 'api']);
            $user->assignRole($role->name);

            if ($user) {
                $identifier = $user->id . '_type-Reg';
                $token = new Token($identifier);
                $otpResponse = $token->getToken();
                $responseArray = $otpResponse->getData(true);
                $otpCode = $responseArray['token']['token'];
                $user->email_verification_token = $otpCode;
                $user->save();

                $token->addOTPVerification($user->id, $otpCode);

                $verificationBaseUrl = $this->verificationUrl;
                $verificationLink = "{$verificationBaseUrl}?userId={$user->id}&token={$otpCode}";

                try {
                    Mail::to($user->email)->send(new OTPMailer($user, $verificationLink));
                } catch (\Exception $emailException) {
                    Log::error('Email sending failed:', [
                        'exception' => $emailException->getMessage(),
                        'line' => $emailException->getLine(),
                        'email' => $user->email
                    ]);
                }
            }

            JWTAuth::factory()->setTTL(20);
            $tokenUser = JWTAuth::fromUser($user);

            $data = [
                'user' => $user,
                'otp' => $otpCode,
                'access_token' => $tokenUser,
                'subscription' => $user->hasSubscription()
            ];

            DB::commit();

            return response()->json([
                'data' => $data,
                'status' => 'success',
                'message' => 'OTP successfully generated'
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->errors(),
                'errors' => $e->errors()
            ], 422);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function requestTokenAgain(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        $user = $this->user->findByEmail($request->email);
        if (!$user) {
            return $this->jsonResponse([], 404, 'User not found', 'error');
        }

        if ($user['is_verified']) {
            return $this->jsonResponse([], 400, 'Email already verified', 'error');
        }

        $identifier = $user['id'] . '_type-Reg';

        try {
            $token = new Token($identifier);
            $otpResponse = $token->getToken();

            if ($otpResponse->getData()) {
                $OtpData = $otpResponse->getData();
            }

            $otpToken = $OtpData->token->token ?? null;
            if ($otpToken === null) {
                throw new \Exception('OTP token not found in the response data.');
            }

            $verificationBaseUrl = $this->verificationUrl;
            $verificationLink = "{$verificationBaseUrl}?userId={$user->id}&token={$otpToken}";

                try {
                    Mail::to($user->email)->send(new OTPMailer($user, $verificationLink));
                } catch (\Exception $emailException) {
                    Log::error('Email sending failed:', [
                        'exception' => $emailException->getMessage(),
                        'line' => $emailException->getLine(),
                        'email' => $user->email
                    ]);
                }
            return response()->json(['status' => 'success', 'message' => 'OTP successfully generated.']);
        } catch (\Exception $e) {
            Log::error('Error in requestTokenAgain: ' . $e->getMessage());
            return $this->jsonResponse([], 500, 'An error occurred while processing your request', 'error');
        }
    }

    public function verifyOtp(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'otp' => 'required|numeric'
        ]);

        $user = $this->user->findByID($request->id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $identifier = $user->id . '_type-Reg';
        $token = new Token($identifier);
        $otpVerification = $token->verifyOtp($request->otp);

        if (!$otpVerification->status) {
            return response()->json(['error' => 'OTP verification failed', 'details' => $otpVerification->message], 400);
        }

        $userVerificationStatus = $this->user->verifyUser($user->id);

        $tokenUser = JWTAuth::fromUser($user);

        $data = [
            'access_token' => $tokenUser,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user,
            'wallet' => $user->hasWallet,
            'profile_picture' => $user->profile ? URL::asset('storage/' . $user->profile->profile_picture) : null,
        ];

        return response()->json([
            'status' => 'success',
            'account_verified' => $userVerificationStatus ? 'yes' : 'no',
            'data' => $data
        ], $userVerificationStatus ? 201 : 400);
    }


    protected function respondWithToken($token)
    {
        $user = $this->auth->user();

        if ($user) {
            $data = [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => $user,
                'wallet' => $user->hasWallet,
                'profile_picture' => $user->profile ? URL::asset('storage/' . $user->profile->profile_picture) : null,
            ];
            return response()->json(['status' => 'success', 'message' => 'success', 'data' => $data], 201);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }
    }

    function UserNumberExists($number)
    {
        return User::whereUserId($number)->exists();
    }
}
