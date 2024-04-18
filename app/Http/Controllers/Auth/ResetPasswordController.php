<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Transformers\Json;
use App\Traits\ResetPasswordTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
     */
    use ResetPasswordTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|string',
                'token' => 'required|string',
                'password' => 'required|string|confirmed'
            ]);
    
            $reset_password_status = Password::reset($credentials, function ($user, $password) {
                $user->password = $password;
                $user->temp_pass = $password;
                $user->save();
            });
    
            if ($reset_password_status == Password::INVALID_TOKEN) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Invalid token provided'
                ], 400);
            }
    
            return response()->json([
                'status' => 'success', 
                'message' => 'Password has been successfully changed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'An error occurred while resetting the password. '. $e->getMessage()
            ], 500);
        }
    }
    

}
