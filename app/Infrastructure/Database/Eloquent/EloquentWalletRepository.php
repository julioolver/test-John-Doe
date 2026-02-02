<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Eloquent;

use App\Application\Wallet\Contracts\WalletRepository;
use App\Domain\Wallet\Entity\Wallet;
use App\Models\Wallet as WalletModel;
use App\Domain\Shared\ValueObjects\Money;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObjects\Document;
use App\Domain\User\ValueObjects\DocumentType;

class EloquentWalletRepository implements WalletRepository
{
    public function getByUserId(int $userId): Wallet {
        $model = WalletModel::where('user_id', $userId)->first();

        $user = new User(
            name: $model->user->name,
            email: $model->user->email,
            document: Document::from($model->user->document, DocumentType::from($model->user->document_type)),
        );

        return new Wallet(
            balance: Money::fromCents($model->balance),
            user: $user,
            id: (string) $model->getKey(),
        );
    }
}
