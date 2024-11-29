<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GreetController;

Route::get('/greet', [GreetController::class, 'greet']);
Route::get('/dontGreet', [GreetController::class, 'dontGreet']);
Route::post('/insert', [GreetController::class, 'insert']);
Route::get('/retrieve', [GreetController::class, 'retrieve']);
