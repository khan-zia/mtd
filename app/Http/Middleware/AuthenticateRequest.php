<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateRequest
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
        /**
         * The request must have an authentication token set.
         * The token could also be in a session
         */
        $token = '';

        if (!$request->has('token')) {
            // Check if the token is in the session
            if (!session('auth_token')) {
                return response('Unauthenticated', 401);
            }

            $token = session('auth_token');
        } else {
            // Prioritize the token defined in the request.
            $token = $request->get('token');
        }

        /**
         * The token must match the preset token in the env
         */
        if ($token !== config('app.auth_token')) {
            // Make sure to invalidate session if present
            if (session()->has('auth_token')) {
                $request->session()->forget('auth_token');
            }

            return response('Unauthenticated', 401);
        }

        /**
         * If the token did not come from the session, then set the session.
         */
        if (!session()->has('auth_token')) {
            session()->put('auth_token', $token);
            session()->save();
        }

        return $next($request);
    }
}
