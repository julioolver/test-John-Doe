<?php

namespace App\Exceptions;

use App\Domain\Transfer\Exception\AuthorizationDeniedException;
use Illuminate\Foundation\Configuration\Exceptions;
use RuntimeException;

class TransferExceptionMapper
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->render(function (AuthorizationDeniedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        });

        $exceptions->render(function (RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 502);
        });
    }
}
