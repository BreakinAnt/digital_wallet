<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWallet extends Model
{
    use SoftDeletes, HasFactory;

    protected $with = ['currency'];

    protected $fillable = [
        'user_id',
        'currency_id',
        'balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function transactions()
    {
        return $this->hasMany(UserTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }
}
