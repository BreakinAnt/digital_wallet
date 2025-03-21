<?php

namespace App\Enums;

enum TransactionStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDING = 'refunding';
    case REFUNDED = 'refunded';
}