<?php

declare(strict_types=1);

namespace App\Application\Transfer\UseCases;

use App\Application\Transfer\Contracts\TransferRepository;
use App\Application\Transfer\DTOs\TransferInputDTO;
use App\Application\Transfer\DTOs\TransferOutputDTO;
use App\Application\Shared\Contracts\TransactionManager;
use App\Application\Wallet\Contracts\WalletRepository;
use App\Domain\Transfer\Entity\Transfer;
use App\Domain\Transfer\Enums\TransferStatus;
use App\Domain\Wallet\Entity\Wallet;
use Throwable;

class CreateTransferUseCase
{
    public function __construct(
        private TransferRepository $transferRepository,
        private WalletRepository $walletRepository,
        private TransactionManager $transactionManager,
    ) {}

    public function execute(TransferInputDTO $request): TransferOutputDTO
    {
        try {
            $createdTransfer = $this->transactionManager->run(function () use ($request) {
                $payer = $this->walletRepository->getByUserIdForUpdate($request->payerId);
                $payee = $this->walletRepository->getByUserIdForUpdate($request->payeeId);

                $payer->user->assertCanTransfer();

                $transfer = $this->createTransfer($request, $payer, $payee);
                $transfer->execute();

                $this->walletRepository->updateBalance($request->payerId, $payer->balance);
                $this->walletRepository->updateBalance($request->payeeId, $payee->balance);

                return $this->transferRepository->create($transfer);
            });
        } catch (Throwable $exception) {
            $this->recordFailedTransfer($request);

            throw $exception;
        }

        return new TransferOutputDTO($createdTransfer);
    }

    private function createTransfer(TransferInputDTO $request, Wallet $payer, Wallet $payee): Transfer
    {
        return new Transfer(
            payer: $payer,
            payee: $payee,
            amount: $request->amount,
            createdAt: $request->createdAt,
        );
    }

    private function recordFailedTransfer(TransferInputDTO $request): void
    {
        try {
            $payer = $this->walletRepository->getByUserId($request->payerId);
            $payee = $this->walletRepository->getByUserId($request->payeeId);
        } catch (Throwable) {
            return;
        }

        $failedTransfer = new Transfer(
            payer: $payer,
            payee: $payee,
            amount: $request->amount,
            status: TransferStatus::FAILED,
            createdAt: $request->createdAt,
        );

        $this->transferRepository->create($failedTransfer);
    }
}
