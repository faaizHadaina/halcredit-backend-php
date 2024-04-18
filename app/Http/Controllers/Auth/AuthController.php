<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Token;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public $auth;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    protected $userInvestments;
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->auth = auth()->guard('api');
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = $this->auth->attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials provided'
                ], 400);
            }

            return $this->respondWithToken($token);

        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log the user out (Inv$accessToken = $request->query('accessToken');validate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->auth->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $currentToken = JWTAuth::getToken();
            if (!$currentToken) {
                return response()->json(['error' => 'Token not provided'], 401);
            }

            $user = $this->auth->user();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $newToken = JWTAuth::fromUser($user);
            JWTAuth::factory()->setTTL(20);

            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not refresh the token: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }

    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = $this->auth->user();

        if ($user) {
            $data = [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL(),
                'user' => $user,
                'wallet' => $user->hasWallet,
                'profile_picture' => $user->profile ? URL::asset('storage/' . $user->profile->profile_picture) : null,
            ];
            return response()->json(['status' => 'success', 'data' => $data], 201);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
