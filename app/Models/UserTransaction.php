<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_id',
        'target_walled_id',
        'status_id',
        'currency_id',
        'amount',
        'type',
        'completed_at',
        'cancelled_at',
    ];

    protected $dates = [
        'completed_at',
        'cancelled_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(UserWallet::class);
    }

    public function targetWallet()
    {
        return $this->belongsTo(UserWallet::class, 'target_walled_id');
    }

    public function status()
    {
        return $this->hasMany(TransactionStatus::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
