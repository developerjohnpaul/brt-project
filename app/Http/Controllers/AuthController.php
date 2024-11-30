<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Register function
    public function register(Request $request)
    {
        $payload = $request->json()->all();
        \Log::info('Request payload:', $payload);
        \Log::info('Starting registration process');
    
        $validator = Validator::make($payload, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            \Log::error('Validation errors:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validated = $validator->validated();
    
        $user = DB::insert(
            'INSERT INTO users (name, email, password) VALUES (?, ?, ?)',
            [$validated['name'], $validated['email'], bcrypt($validated['password'])]
        );
    
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    // Login function
    public function login(Request $request)
    {
        try {
            $payload = $request->json()->all();
            \Log::info('Login payload:', $payload);
    
            $validator = Validator::make($payload, [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);
    
            if ($validator->fails()) {
                \Log::error('Validation errors:', $validator->errors()->toArray());
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $validated = $validator->validated();
    
            $user = User::where('email', $validated['email'])->first();
    
            if (!$user || !password_verify($validated['password'], $user->password)) {
                \Log::warning('Invalid credentials for email: ' . $validated['email']);
                return response()->json(['error' => 'Invalid email or password'], 401);
            }
    
            if (!$token = JWTAuth::fromUser($user)) {
                \Log::error('Failed to generate token for user: ' . $validated['email']);
                return response()->json(['error' => 'Could not create token'], 500);
            }
    
            \Log::info('User successfully logged in: ' . $validated['email']);
    
            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Login Error:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function loginByToken(Request $request)
    {
        try {
            // Ensure the Authorization header is set
            if (!$request->header('Authorization')) {
                \Log::error('Authorization header missing');
                return response()->json(['error' => 'Authorization header missing'], 400);
            }
    
            // Parse the token and authenticate
            $user = JWTAuth::parseToken()->authenticate();
    
            if (!$user) {
                \Log::warning('User not found for provided token');
                return response()->json(['error' => 'User not found'], 404);
            }
    
            \Log::info('User authenticated successfully via token: ' . $user->email);
    
            // Return the user details
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 200);
    
        } catch (JWTException $e) {
            \Log::error('Token authentication failed:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid or expired token'], 401);
        } catch (\Exception $e) {
            \Log::error('An unexpected error occurred:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    
    }
}
