<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use carbon\Carbon;
class SubscriptionMiddleware
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
        // dd($user->email);
        // dd($user->hasSubscription);
        if(isset($user->hasSubscription) && $user->hasSubscription){
            $subscription = $user->hasSubscription;
            if($subscription->from != null && $subscription->to != null){
                $from = Carbon::parse($subscription->from);
                $to = Carbon::parse($subscription->to);
                $now = Carbon::now();
                if($from->lte($now) && $to->gte($now)){
                    return $next($request);
                }else{
                    return response()->json(['Permission' => 'not found', 'message' => 'User needs to subscribe. Redirect user to subcription page.'], 401);
                }
            }else{
                return $next($request);
            }

        }else{
            return response()->json(['Permission' => 'not found', 'message' => 'User has no subscription at the moment'], 401);
        }
    }
}
