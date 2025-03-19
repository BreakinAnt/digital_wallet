<?php

use App\Services\UserService;
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