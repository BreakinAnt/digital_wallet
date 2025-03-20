<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'target_user_id',
        'currency_id',
        'amount',
        'total_amount',
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

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function statuses()
    {
        return $this->hasMany(TransactionStatus::class, 'transaction_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
