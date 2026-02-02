<?php

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;

class ExceptionMapper
{
    public static function register(Exceptions $exceptions): void
    {
        UserExceptionMapper::register($exceptions);
        WalletExceptionMapper::register($exceptions);
    }
}
