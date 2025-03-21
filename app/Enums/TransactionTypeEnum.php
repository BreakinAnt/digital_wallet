<?php
namespace App\Enums;

enum TransactionTypeEnum: string {
    case TRANSFER = 'transfer';
    case DEPOSIT  = 'deposit';
    case WITHDRAW = 'withdraw';

    public static function toArray(): array
    {
        return [
            'transfer',
            'deposit',
            'withdraw',
        ];
    }

    public static function fromString(string $value): ?self
    {
        return match ($value) {
            'transfer' => self::TRANSFER,
            'deposit' => self::DEPOSIT,
            'withdraw' => self::WITHDRAW,
            default => null,
        };
    }

}
