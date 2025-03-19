<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/user/login', [AuthController::class, 'login']);
Route::post('/user', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});