<?php

declare(strict_types=1);

namespace App\Application\Transfer\Contracts;

interface NotificationGateway
{
    public function notify(int $payerId, int $payeeId, int $amountCents): void;
}
