<?php

namespace App\Models;

use App\Domain\Transfer\Enums\TransferStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $fillable = [
        'payer_id',
        'payee_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'integer',
        'status' => TransferStatus::class,
    ];

    protected $attributes = [
        'status' => TransferStatus::PENDING,
    ];

    /**
     * Get the payer that owns the transfer.
     *
     * @return BelongsTo<Wallet, $this>
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the payee that owns the transfer.
     *
     * @return BelongsTo<Wallet, $this>
     */
    public function payee(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
