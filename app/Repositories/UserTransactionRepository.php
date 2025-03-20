<?php

namespace App\Repositories;

use App\Models\Currency;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\UserWallet;

class UserTransactionRepository
{
    public function getById(int $id): UserTransaction
    {
        return UserTransaction::findOrFail($id);
    }

    public function create(User $user, User $targetUser, Currency $currency, int $amount, string $type = 'deposit'): UserTransaction
    {
        return UserTransaction::create([
            'user_id' => $user->id,
            'target_user_id' => $targetUser->id,
            'currency_id' => $currency->id,
            'amount' => $amount,
            'type' => $type,
        ]);
    }
}