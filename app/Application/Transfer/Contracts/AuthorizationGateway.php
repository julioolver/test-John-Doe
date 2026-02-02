<?php

declare(strict_types=1);

namespace App\Application\Transfer\Contracts;

interface AuthorizationGateway
{
    public function authorize(): bool;
}
