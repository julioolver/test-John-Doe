<?php

namespace App\Http\Controllers;

use App\Application\Transfer\DTOs\TransferInputDTO;
use App\Application\Transfer\UseCases\CreateTransferUseCase;
use App\Domain\Shared\ValueObjects\Money;
use App\Http\Requests\Transfer\CreateTransferRequest;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    public function __construct(
        private CreateTransferUseCase $createTransferUseCase,
    ) {}

    public function store(CreateTransferRequest $request): JsonResponse
    {
        $payerId = $request->integer('payer');
        $payeeId = $request->integer('payee');
        $value = $request->string('value')->toString();

        $transferInputDTO = new TransferInputDTO(
            payerId: $payerId,
            payeeId: $payeeId,
            amount: Money::fromDecimal($value),
        );

        $transferOutputDTO = $this->createTransferUseCase->execute($transferInputDTO);

        return response()->json($transferOutputDTO, 201);
    }
}
