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
        $validator = Validator::make($payload, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
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
    
            $validator = Validator::make($payload, [
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            $validated = $validator->validated();
    
            $user = User::where('email', $validated['email'])->first();
    
            if (!$user || !password_verify($validated['password'], $user->password)) {
                return response()->json(['error' => 'Invalid email or password'], 401);
            }
    
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'Could not create token'], 500);
            }
            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function loginByToken(Request $request)
    {
        try {
            // Ensure the Authorization header is set
            if (!$request->header('Authorization')) {
                return response()->json(['error' => 'Authorization header missing'], 400);
            }
    
            // Parse the token and authenticate
            $user = JWTAuth::parseToken()->authenticate();
    
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            // Return the user details
            return response()->json([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ], 200);
    
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    
    }
}
