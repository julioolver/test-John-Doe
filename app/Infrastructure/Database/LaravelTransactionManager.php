<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Application\Shared\Contracts\TransactionManager;
use Illuminate\Support\Facades\DB;

class LaravelTransactionManager implements TransactionManager
{
    /**
     * @template T
     * @param \Closure(): T $callback
     * @return T
     */
    public function run(callable $callback): mixed
    {
        /** @var \Closure(): T $callback */
        return DB::transaction($callback);
    }
}
