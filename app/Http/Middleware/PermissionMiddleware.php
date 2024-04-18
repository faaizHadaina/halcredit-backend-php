<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Route;
use Session;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionMiddleware
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
        $user = auth()->guard('api')->user();
		$routeName = $request->route()->getName();
		if(Permission::where('name', $routeName)->first()){
            if ($user->can($routeName) || $user->hasPermissionTo($routeName)) {
                return $next($request);
            }
            else
            {	
                return response()->json(['status' => 'error', 'message' => "Access Denied. You do not have this permission."], 405);
            }
        }else{
            return $next($request);
        }
    }
}
