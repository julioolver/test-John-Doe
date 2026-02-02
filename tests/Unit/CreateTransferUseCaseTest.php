<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Shared\Contracts\TransactionManager;
use App\Application\Transfer\Contracts\AuthorizationGateway;
use App\Application\Transfer\Contracts\NotificationGateway;
use App\Application\Transfer\Contracts\TransferRepository;
use App\Application\Transfer\DTOs\TransferInputDTO;
use App\Application\Transfer\UseCases\CreateTransferUseCase;
use App\Application\Wallet\Contracts\WalletRepository;
use App\Domain\Shared\ValueObjects\Money;
use App\Domain\Transfer\Entity\Transfer;
use App\Domain\Transfer\Enums\TransferStatus;
use App\Domain\Transfer\Exception\AuthorizationDeniedException;
use App\Domain\User\Entity\User;
use App\Domain\User\ValueObjects\Document;
use App\Domain\User\ValueObjects\DocumentType;
use App\Domain\Wallet\Entity\Wallet;
use PHPUnit\Framework\TestCase;

class CreateTransferUseCaseTest extends TestCase
{
    public function testSuccessfulTransferDebitsAndCreditsAndNotifies(): void
    {
        $walletRepo = new InMemoryWalletRepository([
            1 => new Wallet(Money::fromCents(10000), $this->makeUser('1'), '1'),
            2 => new Wallet(Money::fromCents(0), $this->makeUser('2'), '2'),
        ]);
        $transferRepo = new InMemoryTransferRepository();
        $authorization = new StubAuthorizationGateway(true);
        $notification = new SpyNotificationGateway();
        $transactionManager = new PassthroughTransactionManager();

        $useCase = new CreateTransferUseCase(
            $transferRepo,
            $walletRepo,
            $transactionManager,
            $authorization,
            $notification
        );

        $input = new TransferInputDTO(
            payerId: 1,
            payeeId: 2,
            amount: Money::fromCents(10000)
        );

        $output = $useCase->execute($input);

        $this->assertSame('success', $output->transfer->status->value);
        $this->assertSame(0, $walletRepo->getByUserId(1)->balance->cents());
        $this->assertSame(10000, $walletRepo->getByUserId(2)->balance->cents());
        $this->assertTrue($notification->called);
        $this->assertCount(1, $transferRepo->created);
    }

    public function testAuthorizationDeniedRecordsFailedTransferAndThrows(): void
    {
        $walletRepo = new InMemoryWalletRepository([
            1 => new Wallet(Money::fromCents(10000), $this->makeUser('1'), '1'),
            2 => new Wallet(Money::fromCents(0), $this->makeUser('2'), '2'),
        ]);
        $transferRepo = new InMemoryTransferRepository();
        $authorization = new StubAuthorizationGateway(false);
        $notification = new SpyNotificationGateway();
        $transactionManager = new PassthroughTransactionManager();

        $useCase = new CreateTransferUseCase(
            $transferRepo,
            $walletRepo,
            $transactionManager,
            $authorization,
            $notification
        );

        $input = new TransferInputDTO(
            payerId: 1,
            payeeId: 2,
            amount: Money::fromCents(10000)
        );

        $this->expectException(AuthorizationDeniedException::class);

        try {
            $useCase->execute($input);
        } finally {
            $this->assertCount(1, $transferRepo->created);
            $this->assertSame(TransferStatus::FAILED, $transferRepo->created[0]->status);
            $this->assertFalse($notification->called);
        }
    }

    public function testNotificationFailureDoesNotBreakTransfer(): void
    {
        $walletRepo = new InMemoryWalletRepository([
            1 => new Wallet(Money::fromCents(10000), $this->makeUser('1'), '1'),
            2 => new Wallet(Money::fromCents(0), $this->makeUser('2'), '2'),
        ]);
        $transferRepo = new InMemoryTransferRepository();
        $authorization = new StubAuthorizationGateway(true);
        $notification = new SpyNotificationGateway(true);
        $transactionManager = new PassthroughTransactionManager();

        $useCase = new CreateTransferUseCase(
            $transferRepo,
            $walletRepo,
            $transactionManager,
            $authorization,
            $notification
        );

        $input = new TransferInputDTO(
            payerId: 1,
            payeeId: 2,
            amount: Money::fromCents(10000)
        );

        $output = $useCase->execute($input);

        $this->assertSame('success', $output->transfer->status->value);
        $this->assertCount(1, $transferRepo->created);
        $this->assertTrue($notification->called);
    }

    private function makeUser(string $id): User
    {
        return new User(
            name: 'User '.$id,
            email: 'user'.$id.'@example.com',
            document: Document::from('12345678901', DocumentType::CPF),
            id: $id
        );
    }
}

final class InMemoryWalletRepository implements WalletRepository
{
    /** @var array<int, Wallet> */
    private array $wallets;

    /**
     * @param array<int, Wallet> $wallets
     */
    public function __construct(array $wallets)
    {
        $this->wallets = $wallets;
    }

    public function getByUserId(int $userId): Wallet
    {
        return $this->wallets[$userId];
    }

    public function getByUserIdForUpdate(int $userId): Wallet
    {
        return $this->getByUserId($userId);
    }

    public function updateBalance(int $userId, Money $amount): bool
    {
        $wallet = $this->wallets[$userId] ?? null;

        if (! $wallet) {
            return false;
        }

        $this->wallets[$userId] = new Wallet($amount, $wallet->user, $wallet->id);

        return true;
    }
}

final class InMemoryTransferRepository implements TransferRepository
{
    /** @var list<Transfer> */
    public array $created = [];

    public function create(Transfer $transfer): Transfer
    {
        $this->created[] = $transfer;

        return $transfer;
    }

    public function findById(string $id): ?Transfer
    {
        return null;
    }
}

final class StubAuthorizationGateway implements AuthorizationGateway
{
    public function __construct(private bool $authorized)
    {
    }

    public function authorize(): bool
    {
        if (! $this->authorized) {
            throw new AuthorizationDeniedException('Transfer not authorized.');
        }

        return true;
    }
}

final class SpyNotificationGateway implements NotificationGateway
{
    public bool $called = false;

    public function __construct(private bool $throw = false)
    {
    }

    public function notify(int $payerId, int $payeeId, int $amountCents): void
    {
        $this->called = true;

        if ($this->throw) {
            throw new \RuntimeException('Notification failed.');
        }
    }
}

final class PassthroughTransactionManager implements TransactionManager
{
    public function run(callable $callback): mixed
    {
        return $callback();
    }
}
