<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BRT;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BRTController extends Controller
{
    // Store BRT
    public function store(Request $request)
    {
        \Log::info('Store method accessed.');
        \Log::info('Authenticated User ID:', [Auth::id()]);
        // Capture payload
        $payload = $request->json()->all();
        \Log::info('Payload:', $payload);
    
        // Validate payload
        $validator = Validator::make($payload, [
            'brt_code' => 'required|string|unique:brts,brt_code',
            'reserved_amount' => 'required|numeric',
            'status' => 'required|in:active,expired',
        ]);
        \Log::info('Validation completed.');
    
        if ($validator->fails()) {
            \Log::error('Validation errors:', $validator->errors()->toArray());
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
    
        // Attempt to create BRT record
        try {
            $brt = BRT::create([
                'user_id' => Auth::id(),
                'brt_code' => $payload['brt_code'],
                'reserved_amount' => $payload['reserved_amount'],
                'status' => $payload['status'],
            ]);
            \Log::info('BRT record created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating BRT record:', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    
        return response()->json(['success' => true, 'data' => $brt], 201);
    }
    

    // Show all BRTs for the authenticated user
    public function index()
    {
        $brts = BRT::where('user_id', Auth::id())->get();
        return response()->json(['success' => true, 'data' => $brts]);
    }

    // Show a specific BRT
    public function show($id)
    {
        try {
            $brt = BRT::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('BRT record not found for ID:', ['id' => $id]);
            return response()->json(['error' => 'Record not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $brt]);
    }

    // Update BRT
    public function update(Request $request, $id)
    {
       $payload = $request->json()->all();
       \Log::info('Update payload received:', ['payload' => $payload]);

        $validator = Validator::make($payload, [
            'reserved_amount' => 'required|numeric',
            'status' => 'required|in:active,expired',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        try {
            $brt = BRT::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'Record not found'], 404);
        }
        

        $brt->update([
            'reserved_amount' => $payload['reserved_amount'],
            'status' => $payload['status'],
        ]);

        return response()->json(['success' => true, 'data' => $brt]);
    }

    // Delete BRT
    public function destroy($id)
    {
        $brt = BRT::findOrFail($id);
        $brt->delete();

        return response()->json(['success' => true, 'message' => 'BRT deleted']);
    }
}
