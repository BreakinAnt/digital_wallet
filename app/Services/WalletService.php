<?php

namespace App\Services;

use App\Enums\TransactionStatusEnum;
use App\Exceptions\UserException;
use App\Models\Currency;
use App\Models\TransactionStatus;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\UserWallet;
use App\Repositories\TransactionStatusRepository;
use App\Repositories\UserTransactionRepository;
use App\Repositories\UserWalletRepository;

class WalletService
{
    protected $userWalletRep, $userTransactionRep, $transactionStatusRep;

    public function __construct()
    {
        $this->userWalletRep = new UserWalletRepository();
        $this->userTransactionRep = new UserTransactionRepository();
        $this->transactionStatusRep = new TransactionStatusRepository();
    }

    public function getWallet(User $user): UserWallet
    {
        $wallet = $this->userWalletRep->getByUser($user);

        return $wallet;
    }

    public function getTransaction(int $transaction): UserTransaction
    {
        $transaction = $this->userTransactionRep->getById($transaction);

        return $transaction;
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
        
        $transaction = $this->userTransactionRep->create($user, $targetUser, $currency, $amount, $type);

        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::PENDING);
  
        return $transaction;
    }

    public function completeTransaction(UserTransaction $transaction): UserWallet
    {
        if($transaction->completed_at !== null) {
            throw new UserException('Transaction has already been completed');
        }

        if($transaction->cancelled_at !== null) {
            throw new UserException('Cannot complete a cancelled transaction');
        }

        $wallet = $this->getWallet($transaction->user);

        $targetWallet = $this->getWallet($transaction->targetUser);

        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::COMPLETED);

        $transaction->update(['completed_at' => now()]);

        $this->userWalletRep->update($wallet, -$transaction->amount);
        $this->userWalletRep->update($targetWallet, $transaction->amount);

        return $wallet;
    }

    public function sendRefund(UserTransaction $transaction): UserTransaction
    {
        if ($transaction->completed_at !== null) {
            throw new UserException('Cannot refund a completed transaction');
        }

        if ($transaction->cancelled_at !== null) {
            throw new UserException('Transaction has already been refunded');
        }

        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::REFUNDING);

        return $transaction;
    }

    public function completeRefund(UserTransaction $transaction): UserWallet
    {
        $wallet = $this->getWallet($transaction->user);

        $targetWallet = $this->getWallet($transaction->targetUser);

        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::REFUNDED);

        $transaction->update(['cancelled_at' => now()]);

        $this->userWalletRep->update($wallet, $transaction->amount);
        $this->userWalletRep->update($targetWallet, -$transaction->amount);

        return $wallet;
    }
}
