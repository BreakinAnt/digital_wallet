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
        //Get the wallet of the user, if it exists, otherwise create a new wallet
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
        //Get the wallet of the user
        $wallet = $this->getWallet($user);

        //Validation checks
        if ($user === $targetUser) {
            throw new UserException('You cannot send a transaction to yourself');
        }
        if ($wallet->balance < $amount) {
            throw new UserException('Insufficient balance to complete the transaction');
        }

       return $this->sendTransaction($user, $targetUser, $amount, TransactionTypeEnum::TRANSFER);
    }

    public function sendDeposit(User $user, int $amount): UserTransaction
    {
        return $this->sendTransaction($user, $user, $amount, TransactionTypeEnum::DEPOSIT);
    }

    public function sendWithdraw(User $user, int $amount): UserTransaction
    {
        return $this->sendTransaction($user, $user, -1 * $amount, TransactionTypeEnum::WITHDRAW);
    }

    //Create the transaction, by creating a new transaction record and setting the transaction status to pending
    private function sendTransaction(User $user, User $targetUser, int $amount, TransactionTypeEnum $type): UserTransaction
    {        
        //Create a new transaction record
        $transaction = $this->userTransactionRep->create($user, $targetUser, $user->wallet->currency, $amount, $type);

        //Update the transaction status to pending
        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::PENDING);
  
        return $transaction;
    }

    //Complete the transaction, by updating the transaction status and the wallet balance
    public function completeTransaction(UserTransaction $transaction): UserTransaction
    {
        //Get the wallet of the user and the target user
        $wallet = $this->getWallet($transaction->user);
        $targetWallet = $this->getWallet($transaction->targetUser);

        //Validation checks
        if($transaction->completed_at !== null) {
            throw new UserException('Transaction has already been completed');
        }
        if($transaction->cancelled_at !== null) {
            throw new UserException('Cannot complete a cancelled transaction');
        } 
        if($transaction->total_amount > $wallet->balance) {
            throw new UserException('Insufficient balance to complete the transaction');
        }

        try {
            $transaction = $this->exchangeRate($transaction->fresh());
        } catch (Exception $e) {
            report($e);
            throw new Exception('An unexpected error occurred. Please try again later.');
        }

        //Update the transaction status, and completed_at timestamp
        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::COMPLETED);
        $transaction->update(['completed_at' => now()]);
        
        //Update the wallet balance
        if(TransactionTypeEnum::fromString($transaction->type) === TransactionTypeEnum::TRANSFER) {
            $this->userWalletRep->update($wallet, -$transaction->amount);
        }
        $this->userWalletRep->update($targetWallet, $transaction->total_amount);

        return $transaction;
    }

    //Cancel the transaction while is still pending, by updating the transaction status and the transaction record
    public function cancelTransaction(UserTransaction $transaction): UserTransaction
    {
        //Validation checks
        if($transaction->cancelled_at !== null) {
            throw new UserException('Transaction has already been cancelled');
        }
        if($transaction->completed_at !== null) {
            throw new UserException('Cannot cancel a completed transaction');
        }

        //Update the transaction status, by setting the status to cancelled
        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::CANCELLED);

        //Update the transaction record, by setting the cancelled_at timestamp
        $transaction->update(['cancelled_at' => now()]);

        return $transaction;
    }

    //Create a refund request, by updating the transaction status to refunding
    public function sendRefund(UserTransaction $transaction): UserTransaction
    {
        //Validation checks
        if($transaction->completed_at === null) {
            $this->cancelTransaction($transaction);
        }
        if ($transaction->cancelled_at !== null) {
            throw new UserException('Transaction has already been refunded');
        }

        //Update the transaction status to refunding
        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::REFUNDING);

        return $transaction;
    }

    //Complete the refund, by updating the transaction status and the wallet balance
    public function completeRefund(UserTransaction $transaction): UserWallet
    {
        //Validation checks
        if($transaction->completed_at === null) {
            throw new UserException('Cannot refund an incomplete transaction');
        }

        //Get the wallet of the user and the target user
        $wallet = $this->getWallet($transaction->user);
        $targetWallet = $this->getWallet($transaction->targetUser);

        //Update the transaction status, and cancelled_at timestamp
        $this->transactionStatusRep->create($transaction, TransactionStatusEnum::REFUNDED);
        $transaction->update(['cancelled_at' => now()]);

        //Update the wallet balance
        $this->userWalletRep->update($wallet, $transaction->amount);
        $this->userWalletRep->update($targetWallet, -$transaction->total_amount ?? $transaction->amount);

        return $wallet;
    }

    public function exchangeRate(UserTransaction $transaction): UserTransaction
    {
        //Get the currency of the user and the target user
        $currency = $transaction->user->wallet->currency;
        $targetCurrency = $transaction->targetUser->wallet->currency;

        //If the currency of the user and the target user are the same, return the transaction
        if($currency->code === $targetCurrency->code) {
            $transaction->update(['total_amount' => $transaction->amount]);

            return $transaction;
        }

        //Get the exchange rate of the currency of the user to the currency of the target user
        try {
            $response = Http::get("https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/".strtolower($currency->code).".json");
        } catch (Exception $e) {
            $response = Http::get("https://latest.currency-api.pages.dev/v1/currencies/".strtolower($currency->code).".json");
        }

        //Calculate the total amount of the transaction
        $targetCurrencyRate = $response->json()[strtolower($currency->code)][strtolower($targetCurrency->code)];
        $totalAmount = round(($transaction->amount/100) * $targetCurrencyRate)*100;
  
        $transaction->update(['total_amount' => $totalAmount]);

        return $transaction;
    }
}
