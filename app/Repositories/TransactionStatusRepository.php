<?php

namespace App\Repositories;

use App\Enums\TransactionStatusEnum;
use App\Models\TransactionStatus;
use App\Models\UserTransaction;

class TransactionStatusRepository
{
    public function create(UserTransaction $transaction, TransactionStatusEnum $name): TransactionStatus
    {
        return TransactionStatus::create([
            'transaction_id' => $transaction->id,
            'name' => $name,
        ]);
    }
}