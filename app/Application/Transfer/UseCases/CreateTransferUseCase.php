<?php

declare(strict_types=1);

namespace App\Application\Transfer\UseCases;

use App\Application\Transfer\Contracts\TransferRepository;
use App\Application\Transfer\DTOs\TransferInputDTO;
use App\Application\Transfer\DTOs\TransferOutputDTO;
use App\Application\Shared\Contracts\TransactionManager;
use App\Application\Wallet\Contracts\WalletRepository;
use App\Domain\Transfer\Entity\Transfer;
use App\Domain\Wallet\Entity\Wallet;

class CreateTransferUseCase
{
    public function __construct(
        private TransferRepository $transferRepository,
        private WalletRepository $walletRepository,
        private TransactionManager $transactionManager,
    ) {}

    public function execute(TransferInputDTO $request): TransferOutputDTO
    {
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
}
