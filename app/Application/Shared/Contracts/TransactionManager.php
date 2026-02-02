<?php

declare(strict_types=1);

namespace App\Application\Shared\Contracts;

interface TransactionManager
{
    /**
     * Runs the callback inside a database transaction.
     */
    public function run(callable $callback): mixed;
}
