<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'can_transfer',
    ];

    protected $casts = [
        'balance' => 'integer',
    ];

    protected $appends = [
        'balance_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBalanceAmountAttribute(): string
    {
        return number_format($this->balance / 100, 2, '.', '');
    }
}
