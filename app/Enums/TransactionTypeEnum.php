<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case TRANSFER = 'transfer';
    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';

    static function toArray(): array
    {
        return [
            'transfer' => 'Transfer',
            'deposit' => 'Deposit',
            'withdraw' => 'Withdraw',
        ];
    }


}