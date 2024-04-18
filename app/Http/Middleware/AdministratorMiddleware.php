<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AdministratorMiddleware
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
        if(auth()->guard('api')->check() && (auth()->guard('api')->user()->hasRole('Super_Admin') || auth()->guard('web')->user()->hasRole('Super_Admin'))){
			return $next($request);
		}
		else{
			return response()->json(['status' => 'error', 'message' => "Access Denied. You do not have this permission."], 405);
		}
    }
}
