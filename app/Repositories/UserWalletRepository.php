<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Models\User;
use App\Models\UserWallet;

class UserWalletRepository
{
    public function getByUser(User $user): UserWallet
    {
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = $this->create($user, Currency::first());
        }

        return $wallet;
    }

    public function create(User $user, Currency $currency): UserWallet
    {
        return UserWallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'currency_id' => $currency->id
        ]);
    }

    public function update(UserWallet $wallet, int $amount): UserWallet
    {
        $wallet->balance += $amount;
        $wallet->save();

        return $wallet;
    }
}