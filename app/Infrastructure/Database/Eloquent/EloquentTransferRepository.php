<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Eloquent;

use App\Application\Transfer\Contracts\TransferRepository;
use App\Domain\Shared\ValueObjects\Money;
use App\Domain\Transfer\Entity\Transfer;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObjects\Document;
use App\Domain\User\ValueObjects\DocumentType;
use App\Domain\Wallet\Entity\Wallet as DomainWallet;
use App\Models\Transfer as TransferModel;
use App\Models\Wallet as WalletModel;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;

class EloquentTransferRepository implements TransferRepository
{
    public function create(Transfer $transfer): Transfer
    {
        $payerId = $transfer->payer->id;
        $payeeId = $transfer->payee->id;

        $model = TransferModel::create([
            'payer_id' => $payerId,
            'payee_id' => $payeeId,
            'amount' => $transfer->amount->cents(),
            'status' => $transfer->status,
        ]);

        return $this->toDomain($model);
    }

    public function findById(string $id): ?Transfer
    {
        $model = TransferModel::find($id);

        if (! $model) {
            return null;
        }

        return $this->toDomain($model);
    }

    private function toDomain(TransferModel $model): Transfer
    {
        $payer = $this->mapWallet($model->payer);
        $payee = $this->mapWallet($model->payee);

        return new Transfer(
            payer: $payer,
            payee: $payee,
            amount: Money::fromCents($model->amount),
            status: $model->status,
            createdAt: $model->created_at?->toDateTime() ?? new \DateTime(),
            id: $this->modelKeyToString($model),
        );
    }

    private function mapWallet(?WalletModel $wallet): DomainWallet
    {
        if (! $wallet) {
            throw new InvalidArgumentException('Wallet not found for transfer.');
        }

        if (! $wallet->user) {
            throw new InvalidArgumentException('User not found for wallet.');
        }

        /** @var DocumentType|string $documentTypeValue */
        $documentTypeValue = $wallet->user->document_type;

        $documentType = $documentTypeValue instanceof DocumentType
            ? $documentTypeValue
            : DocumentType::from($documentTypeValue);

        $user = new User(
            name: $wallet->user->name,
            email: $wallet->user->email,
            document: Document::from($wallet->user->document, $documentType),
            id: $this->modelKeyToString($wallet->user),
        );

        return new DomainWallet(
            balance: Money::fromCents($wallet->balance),
            user: $user,
            id: $this->modelKeyToString($wallet),
        );
    }

    private function modelKeyToString(Model $model): ?string
    {
        /** @var int|string|null $key */
        $key = $model->getKey();

        return $key !== null ? (string) $key : null;
    }
}
