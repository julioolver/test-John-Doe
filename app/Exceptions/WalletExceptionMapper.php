<?php

namespace App\Exceptions;

use App\Domain\Wallet\Exception\CannotTransferException;
use App\Domain\Wallet\Exception\InsufficientBalanceException;
use App\Domain\Wallet\Exception\InvalidAmountException;
use Illuminate\Foundation\Configuration\Exceptions;

class WalletExceptionMapper
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->render(function (InvalidAmountException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        });

        $exceptions->render(function (InsufficientBalanceException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        });

        $exceptions->render(function (CannotTransferException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        });
    }
}
