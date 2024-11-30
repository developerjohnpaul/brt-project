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
        \Log::info('Authorization Header:', ['value' => $request->header('Authorization')]);

        try {
            // Extract the token from the Authorization header
            $authHeader = $request->header('Authorization');

            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                \Log::error('Missing or invalid Authorization header');
                return response()->json(['error' => 'Missing or invalid Authorization header'], 401);
            }

            // Remove "Bearer " prefix to get the token
            $token = str_replace('Bearer ', '', $authHeader);

            // Authenticate and decode the token
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                \Log::error('User not found');
                return response()->json(['error' => 'Invalid token or user not found'], 401);
            }

            // Log the authenticated user
            \Log::info('Authenticated User:', ['user' => $user->toArray()]);

            // Proceed with the request
            return $next($request);

        } catch (JWTException $e) {
            \Log::error('JWT Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid or expired token'], 401);
        } catch (\Exception $e) {
            \Log::error('General Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
