<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Get the user that owns the wallet.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBalanceAmountAttribute(): string
    {
        return number_format($this->balance / 100, 2, '.', '');
    }
}
