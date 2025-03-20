<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use App\Models\User;
use App\Models\UserTransaction;
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
    public function sendTransaction(Request $request)
    {
        $request->validate([
            'amount'            => ['required', 'integer'],
            'currency_id'       => ['required', 'exists:currencies,id'],
            'recipient'         => ['required', 'email', 'exists:users,email'],
            'type'              => ['optional', 'string'],
        ]);

        $data = $request->only(['amount', 'currency_id', 'recipient', 'type']);

        $targetUser = User::where('email', $data['recipient'])->first();

        $currency = Currency::find($data['currency_id']);
   
        $transaction = $this->walletServ->sendTransaction(
            $this->user, 
            $targetUser, 
            $currency, 
            $data['amount'], 
            isset($data['type'])
        );
        
        $this->walletServ->completeTransaction($transaction);

        return response()->json([
            'message' => 'Transaction created successfully',
            'transaction' => $transaction,
        ]);
    }
}