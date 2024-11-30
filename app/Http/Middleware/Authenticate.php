<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Extract the token from the Authorization header
            $authHeader = $request->header('Authorization');

            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return response()->json(['error' => 'Missing or invalid Authorization header'], 401);
            }

            // Remove "Bearer " prefix to get the token
            $token = str_replace('Bearer ', '', $authHeader);

            // Authenticate and decode the token
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Invalid token or user not found'], 401);
            }

            // Proceed with the request
            return $next($request);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
