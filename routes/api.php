<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/user/login', [AuthController::class, 'login']);
Route::post('/user', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/transaction/{transaction_id}', [TransactionController::class, 'viewTransaction']);
    Route::patch('/transaction/{transaction_id}/refund', [TransactionController::class, 'refundTransaction']);
    Route::post('/transaction', [TransactionController::class, 'sendTransaction']);
});