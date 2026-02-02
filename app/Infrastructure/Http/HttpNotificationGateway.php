<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Transfer\Contracts\NotificationGateway;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HttpNotificationGateway implements NotificationGateway
{
    public function notify(int $payerId, int $payeeId, int $amountCents): void
    {
        $url = config('services.notification.url');
        $timeout = config('services.notification.timeout', 5);

        if (! is_string($url) || $url === '') {
            throw new RuntimeException('Notification URL is not configured.');
        }

        $timeoutSeconds = $this->getTimeoutSeconds($timeout);

        /** @var Response $response */
        $response = Http::timeout($timeoutSeconds)->post($url, [
            'payer' => $payerId,
            'payee' => $payeeId,
            'amount' => $amountCents,
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Notification service unavailable.');
        }
    }

    private function getTimeoutSeconds(mixed $timeout): int
    {
        $timeoutSeconds = 5;

        if (is_int($timeout)) {
            $timeoutSeconds = $timeout;
        }

        if (! is_int($timeout) && is_numeric($timeout)) {
            $timeoutSeconds = (int) $timeout;
        }

        return $timeoutSeconds;
    }
}
