<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BRTController;




Route::middleware('auth.token')->group(function () {
    Route::post('/brts', [BRTController::class, 'store']);
    Route::get('/brts', [BRTController::class, 'index']);
    Route::get('/brts/{id}', [BRTController::class, 'show']);
    Route::put('/brts/{id}', [BRTController::class, 'update']);
    Route::delete('/brts/{id}', [BRTController::class, 'destroy']);
});

