<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifiedUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = auth()->guard('api')->userOrFail();
            // dd($user);
            if($user->email_verified == null){
                return response()->json(['status' => 'Kindly verify your account first']);
            }
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['status' => 'User Not Found!']);
        }
        return $next($request);
    }
}
