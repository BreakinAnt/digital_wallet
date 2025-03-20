<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UserException;
use App\Http\Requests\SendTransactionRequest;
use App\Models\Currency;
use App\Models\User;
use App\Models\UserTransaction;
use App\Resources\TransactionResource;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController
{
    protected $user, $walletServ;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->walletServ = new WalletService();
    }

    /**
     * View a specific transaction.
     */
    public function viewTransaction($transactionId)
    {
        $transaction = $this->walletServ->getTransaction($transactionId);

        return response()->json([
            'transaction' => new TransactionResource($transaction),
        ]);
    }

    /**
     * Handle transaction creation.
     */
    public function sendTransaction(SendTransactionRequest $request)
    {
        $data = $request->only(['amount', 'currency_id', 'recipient', 'type']);

        $targetUser = User::where('email', $data['recipient'])->first();

        $currency = $this->user->wallet->currency;

        try {
            $transaction = $this->walletServ->sendTransaction(
                $this->user, 
                $targetUser, 
                $currency, 
                $data['amount'], 
                isset($data['type'])
            );
        } catch (UserException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }

        return response()->json([
            'message' => 'Transaction created successfully',
            'transaction' => new TransactionResource($transaction),
        ]);
    }

    /**
     * Handle transaction refund.
     */
    public function refundTransaction($transactionId)
    {
        try {
            $transaction = $this->walletServ->getTransaction($transactionId);

            if ($transaction->user_id !== $this->user->id) {
                return response()->json([
                    'message' => 'You are not authorized to refund this transaction.',
                ], 403);
            }

            $this->walletServ->sendRefund($transaction);

        } catch (UserException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }

        return response()->json([
            'message' => 'Transaction refund requested successfully',
        ]);
    }
}