<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GreetController;
use App\Http\Controllers\AuthController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/loginByToken', [AuthController::class, 'loginByToken']);
Route::get('/dontGreet', [GreetController::class, 'dontGreet']);
Route::post('insert', [GreetController::class, 'insert']);
Route::get('retrieve', [AuthController::class, 'retrieve']);
