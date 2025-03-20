<?php

use App\Models\User;
use App\Services\UserService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/verify-email/{email}', function (Request $request, $email, UserService $userServ) {
    $user = $userServ->getUser($email);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->markEmailAsVerified();

    return response()->json(['message' => 'Email has been verified'], 200);
});

Route::get('/confirm-transaction/{transaction_id}', function ($transactionId, WalletService $walletServ) {
    $transaction = $walletServ->getTransaction($transactionId);

    if (!$transaction) {
        return response()->json(['message' => 'Transaction not found'], 404);
    }

    $walletServ->completeTransaction($transaction);

    return response()->json(['message' => 'Transaction completed'], 200);
});

Route::get('/confirm-refund/{transaction_id}', function ($transactionId, WalletService $walletServ) {
    $transaction = $walletServ->getTransaction($transactionId);

    $walletServ->completeRefund($transaction);

    return response()->json(['message' => 'Transaction refunded'], 200);
});

Route::get('/set-funds/{email}', function (Request $request, $email, WalletService $walletServ) {
    $user = User::where('email', $email)->first();

    $wallet = $walletServ->getWallet($user);

    $wallet->update(['balance' => $request->amount ?? 1099]);

    return response()->json(['message' => 'Funds set'], 200);
});