<?php

namespace App\Exceptions;

use App\Domain\Shared\Exceptions\InvalidDocumentException;
use Illuminate\Foundation\Configuration\Exceptions;

class UserExceptionMapper
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->render(function (InvalidDocumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        });
    }
}
