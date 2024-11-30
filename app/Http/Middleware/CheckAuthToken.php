<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class CheckAuthToken
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
            // Check for the Authorization header and parse the token
            if (!$request->header('Authorization')) {
                return response()->json(['error' => 'Authorization header missing'], 400);
            }

            // Attempt to authenticate the user using the token
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Continue processing the request if the token is valid
            return $next($request);

        } catch (JWTException $e) {
            // If token is missing or invalid
            return response()->json(['error' => 'Invalid or expired token'], 401);
        } catch (\Exception $e) {
            // Handle any other unexpected errors
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
