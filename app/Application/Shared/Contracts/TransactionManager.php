<?php

declare(strict_types=1);

namespace App\Application\Shared\Contracts;

interface TransactionManager
{
    /**
     * Runs the callback inside a database transaction.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function run(callable $callback): mixed;
}
