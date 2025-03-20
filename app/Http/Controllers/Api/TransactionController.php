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
     * Handle transaction creation.
     */
    public function sendTransaction(SendTransactionRequest $request)
    {
        $data = $request->only(['amount', 'currency_id', 'recipient', 'type']);

        $targetUser = User::where('email', $data['recipient'])->first();

        $currency = Currency::find($data['currency_id']);

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
}