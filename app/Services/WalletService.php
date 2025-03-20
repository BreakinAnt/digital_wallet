<?php

namespace App\Services;

use App\Exceptions\UserException;
use App\Models\Currency;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\UserWallet;
use App\Repositories\UserTransactionRepository;
use App\Repositories\UserWalletRepository;

class WalletService
{
    protected $userWalletRep, $userTransactionRep;

    public function __construct()
    {
        $this->userWalletRep = new UserWalletRepository();
        $this->userTransactionRep = new UserTransactionRepository();
    }

    public function getWallet(User $user): UserWallet
    {
        $wallet = $this->userWalletRep->getByUser($user);

        return $wallet;
    }

    public function sendTransaction(User $user, User $targetUser, Currency $currency, int $amount, string $type = 'deposit'): UserTransaction
    {
        if ($user === $targetUser) {
            throw new UserException('You cannot send a transaction to yourself');
        }

        $wallet = $this->getWallet($user);

        if ($wallet->balance < $amount) {
            throw new UserException('Insufficient balance to complete the transaction');
        }
        
        return $this->userTransactionRep->create($user, $targetUser, $currency, $amount, $type);
    }

    public function completeTransaction(UserTransaction $transaction): UserWallet
    {
        $wallet = $this->getWallet($transaction->user);

        $targetWallet = $this->getWallet($transaction->targetUser);

        $transaction->update(['completed_at' => now()]);

        $this->userWalletRep->update($wallet, -$transaction->amount);
        $this->userWalletRep->update($targetWallet, $transaction->amount);

        return $wallet;
    }
}
