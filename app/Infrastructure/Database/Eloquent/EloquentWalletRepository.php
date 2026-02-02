<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Eloquent;

use App\Application\Wallet\Contracts\WalletRepository;
use App\Domain\Shared\ValueObjects\Money;
use App\Domain\Wallet\Entity\Wallet;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObjects\Document;
use App\Domain\User\ValueObjects\DocumentType;
use App\Models\Wallet as WalletModel;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;

class EloquentWalletRepository implements WalletRepository
{
    public function getByUserId(int $userId): Wallet
    {
        $model = WalletModel::where('user_id', $userId)->first();

        return $this->mapToDomain($model);
    }

    public function getByUserIdForUpdate(int $userId): Wallet
    {
        $model = WalletModel::where('user_id', $userId)
            ->lockForUpdate()
            ->first();

        return $this->mapToDomain($model);
    }

    public function updateBalance(int $userId, Money $amount): bool
    {
        $model = WalletModel::where('user_id', $userId)->first();

        if (! $model) {
            return false;
        }

        $model->balance = $amount->cents();
        $model->save();

        return true;
    }

    private function mapToDomain(?WalletModel $model): Wallet
    {
        if (! $model) {
            throw new InvalidArgumentException('Wallet not found for user.');
        }

        if (! $model->user) {
            throw new InvalidArgumentException('User not found for wallet.');
        }

        /** @var DocumentType|string $documentTypeValue */
        $documentTypeValue = $model->user->document_type;

        $documentType = $documentTypeValue instanceof DocumentType
            ? $documentTypeValue
            : DocumentType::from($documentTypeValue);

        $user = new User(
            name: $model->user->name,
            email: $model->user->email,
            document: Document::from($model->user->document, $documentType),
            id: $this->modelKeyToString($model->user),
        );

        return new Wallet(
            balance: Money::fromCents($model->balance),
            user: $user,
            id: $this->modelKeyToString($model),
        );
    }

    private function modelKeyToString(Model $model): ?string
    {
        /** @var int|string|null $key */
        $key = $model->getKey();

        return $key !== null ? (string) $key : null;
    }
}
