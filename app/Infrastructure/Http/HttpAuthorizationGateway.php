<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Application\Transfer\Contracts\AuthorizationGateway;
use App\Domain\Transfer\Exception\AuthorizationDeniedException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HttpAuthorizationGateway implements AuthorizationGateway
{
    public function authorize(): bool
    {
        $url = config('services.authorization.url');
        $timeout = config('services.authorization.timeout', 5);

        if (! is_string($url) || $url === '') {
            throw new RuntimeException('Authorization URL is not configured.');
        }

        $timeoutSeconds = $this->getTimeoutSeconds($timeout);

        /** @var Response $response */
        $response = Http::timeout($timeoutSeconds)->get($url);

        $payload = $response->json();
        $authorized = data_get($payload, 'data.authorization', null);

        if ($authorized === false) {
            throw new AuthorizationDeniedException('Transfer not authorized.');
        }

        if (! $response->successful()) {
            throw new RuntimeException('Authorization service unavailable.');
        }

        return true;
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
