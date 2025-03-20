<?php

namespace App\Services;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\UserException;
use App\Models\Currency;
use App\Models\TransactionStatus;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\UserWallet;
use App\Repositories\TransactionStatusRepository;
use App\Repositories\UserTransactionRepository;
use App\Repositories\UserWalletRepository;
use Exception;
use Illuminate\Support\Facades\Http;

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

    public function sendTransfer(User $user, User $targetUser, int $amount): UserTransaction
    {
        if ($user === $targetUser) {
            throw new UserException('You cannot send a transaction to yourself');
        }

        $wallet = $this->getWallet($user);

        if ($wallet->balance < $amount) {
            throw new UserException('Insufficient balance to complete the transaction');
        }

       return $this->sendTransaction($user, $targetUser, $amount, TransactionTypeEnum::TRANSFER);
    }

    private function sendTransaction(User $user, User $targetUser, int $amount, TransactionTypeEnum $type): UserTransaction
    {        
        $transaction = $this->userTransactionRep->create($user, $targetUser, $user->wallet->currency, $amount, $type);

        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::PENDING);
  
        return $transaction;
    }

    public function completeTransaction(UserTransaction $transaction): UserTransaction
    {
        if($transaction->completed_at !== null) {
            throw new UserException('Transaction has already been completed');
        }

        if($transaction->cancelled_at !== null) {
            throw new UserException('Cannot complete a cancelled transaction');
        }

        $wallet = $this->getWallet($transaction->user);

        $targetWallet = $this->getWallet($transaction->targetUser);

        try {
            $transaction = $this->exchangeRate($transaction->fresh());
        } catch (Exception $e) {
            report($e);
            throw new Exception('An unexpected error occurred. Please try again later.');
        }

        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::COMPLETED);

        $transaction->update(['completed_at' => now()]);

        $this->userWalletRep->update($wallet, -$transaction->amount);
        $this->userWalletRep->update($targetWallet, $transaction->total_amount);

        return $transaction;
    }

    public function cancelTransaction(UserTransaction $transaction): UserTransaction
    {
        if($transaction->cancelled_at !== null) {
            throw new UserException('Transaction has already been cancelled');
        }

        if($transaction->completed_at !== null) {
            throw new UserException('Cannot cancel a completed transaction');
        }

        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::CANCELLED);

        $transaction->update(['cancelled_at' => now()]);

        return $transaction;
    }

    public function sendRefund(UserTransaction $transaction): UserTransaction
    {
        if($transaction->completed_at === null) {
            $this->cancelTransaction($transaction);
        }

        if ($transaction->cancelled_at !== null) {
            throw new UserException('Transaction has already been refunded');
        }

        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::REFUNDING);

        return $transaction;
    }

    public function completeRefund(UserTransaction $transaction): UserWallet
    {
        if($transaction->completed_at === null) {
            throw new UserException('Cannot refund an incomplete transaction');
        }

        $wallet = $this->getWallet($transaction->user);

        $targetWallet = $this->getWallet($transaction->targetUser);

        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::REFUNDED);

        $transaction->update(['cancelled_at' => now()]);

        $this->userWalletRep->update($wallet, $transaction->amount);
        $this->userWalletRep->update($targetWallet, -$transaction->total_amount ?? $transaction->amount);

        return $wallet;
    }

    public function exchangeRate(UserTransaction $transaction): UserTransaction
    {
        $currency = $transaction->user->wallet->currency;
        $targetCurrency = $transaction->targetUser->wallet->currency;

        if($currency->code === $targetCurrency->code) {
            $transaction->update(['total_amount' => $transaction->amount]);

            return $transaction;
        }

        try {
            $response = Http::get("https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/".strtolower($currency->code).".json");
        } catch (Exception $e) {
            $response = Http::get("https://latest.currency-api.pages.dev/v1/currencies/".strtolower($currency->code).".json");
        }


        $targetCurrencyRate = $response->json()[strtolower($currency->code)][strtolower($targetCurrency->code)];

        $totalAmount = round(($transaction->amount/100) * $targetCurrencyRate)*100;
  
        $transaction->update(['total_amount' => $totalAmount]);

        return $transaction;
    }
}
