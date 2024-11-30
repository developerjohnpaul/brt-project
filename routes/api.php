<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GreetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BRTController;




Route::middleware('auth.token')->group(function () {
    Route::post('/brts', [BRTController::class, 'store']);
    Route::get('/brts', [BRTController::class, 'index']);
    Route::get('/brts/{id}', [BRTController::class, 'show']);
    Route::put('/brts/{id}', [BRTController::class, 'update']);
    Route::delete('/brts/{id}', [BRTController::class, 'destroy']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/loginByToken', [AuthController::class, 'loginByToken']);
Route::get('/dontGreet', [GreetController::class, 'dontGreet']);
Route::post('insert', [GreetController::class, 'insert']);
Route::get('retrieve', [AuthController::class, 'retrieve']);
