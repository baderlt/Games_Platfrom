<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BlockUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
           
        if (Auth::check() && Auth::user()->blocked == 1) {
            $reason=auth()->user()->reason;
            Auth::user()->tokens()->delete();
            // abort(403); 
            return response()->json(['status'=>'blocked','message'=>'User Blocked ','reason'=>$reason], 403);
        }
        return $next($request);
    }



    public function terminate()
    {

            $user =User::where("id",'=',auth()->user()->id)->first();
            $user->lastConextion=now();
            $user->save();
          


}
}
