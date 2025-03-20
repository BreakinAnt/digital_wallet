<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    protected $fillable = [
        'transaction_id',
        'name',
    ];

    public function transaction()
    {
        return $this->belongsTo(UserTransaction::class, 'transaction_id');
    }
}
