<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\Token;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use Validator;

class ChangePasswordController extends MyController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = auth()->guard('api')->user();
    }

    public function store(Request $request)
    {
        try {
            $userRecord = User::find($this->auth->id);

            if (!$userRecord) {
                return response()->json(["status" => "error", "message" => "Kindly login to access this endpoint."], 404);
            }

            $validationRules = [
                'otp' => 'required',
                'current_password' => $userRecord->hasPassword ? ['required', new MatchOldPassword] : [],
                'new_password' => $userRecord->hasPassword ? [] : ['required'],
                'new_confirm_password' => $userRecord->hasPassword ? [] : ['same:new_password'],
            ];

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->messages()], 422);
            }

            $identifier = $userRecord->id . '_type-Reg';
            $token = new Token($identifier);
            $otpVerification = $token->verifyOtp($request->otp);

            if (!$otpVerification->status) {
                return response()->json(['status' => 'error', 'message' => 'OTP verification failed', 'details' => $otpVerification->message], 400);
            }

            // $userRecord->update(['password' => $request->new_password, 'hasPassword' => true]);

            return response()->json(['status' => 'success', 'message' => 'Password changed successfully.']);
        } catch (\Exception $e) {
            Log::error('Error in resetPasswordToken: ' . $e->getMessage());
            return $this->jsonResponse([], 500, 'An error occurred while processing your request', 'error');
        }
    }

    public function resetPasswordToken()
    {
        $user = User::find($this->auth->id);

        if (!$user) {
            return $this->jsonResponse([], 404, 'User not found', 'error');
        }

        $identifier = $user->id . '_type-Reg';

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

            Mail::to($user->email)->send(new ResetPassword($user, $otpToken));


            $data = [
                'user' => $user,
                'otp' => $otpToken
            ];

            return response()->json(['status' => 'success', 'message' => 'OTP successfully generated.']);
        } catch (\Exception $e) {
            Log::error('Error in resetPasswordToken: ' . $e->getMessage());
            return $this->jsonResponse([], 500, 'An error occurred while processing your request', 'error');
        }
    }
}
