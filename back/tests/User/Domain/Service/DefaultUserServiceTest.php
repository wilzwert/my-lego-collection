<?php

namespace App\Tests\User\Domain\Service;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Service\TransactionProvider;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\DefaultUserService;
use App\User\Infrastructure\Messenger\UserEventBus;
use MyLegoCollection\SharedEvent\Event\IdentityCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class DefaultUserServiceTest extends TestCase
{
    private EntityId $identityId;
    private EntityId $userId ;
    private UserRepository $userRepository;
    private TransactionProvider $transactionProvider;
    private DefaultUserService $service;

    protected function setUp(): void
    {
        $this->identityId = EntityId::fromString('87654321-e89b-42d3-a456-426614174000');
        $this->userId = EntityId::fromString('123e4567-e89b-42d3-a456-426614174000');

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);
        $this->eventBus = $this->createMock(UserEventBus::class);

        $this->service = new DefaultUserService(
            $this->userRepository,
            $this->transactionProvider,
            $this->eventBus
        );
    }

    #[Test]
    public function shouldCreateUserWithinTransaction(): void
    {
        $event = new IdentityCreatedIntegrationEvent($this->identityId->value());
        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with($event->getId())
            ->willReturn(null);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        // simulate TransactionProvider behavior
        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
                // simulate transaction -> just execute callback
                fn (callable $callback) => $callback()
            );

        $result = $this->service->createUser($this->identityId);

        self::assertInstanceOf(User::class, $result);
        self::assertEquals($this->identityId, $result->getIdentityId());
        self::assertNull($result->getAvatar());
    }

    #[Test]
    public function shouldRetrieveUserByIdentity(): void
    {
        $expectedUser = $this->createMock(User::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with($this->identityId)
            ->willReturn($expectedUser);

        $result = $this->service->getUserByIdentityId($this->identityId);

        self::assertSame($expectedUser, $result);
    }

    #[Test]
    public function shouldRetrieveUserById(): void
    {
        $expectedUser = $this->createMock(User::class);

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with($this->userId)
            ->willReturn($expectedUser);

        $result = $this->service->getUserById($this->userId);

        self::assertSame($expectedUser, $result);
    }

    #[Test]
    public function shouldUpdateAvatar(): void
    {

        $createdAt = new \DateTimeImmutable('2025-11-05T12:00:00');
        $user = new User($this->userId, $this->identityId, $createdAt, $createdAt);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $now = new \DateTimeImmutable();
        $storedFile = new StoredFile(EntityId::generate(), 'stored_filepath', 'stored_filename', 'stored_mimetype', 'stored_extension', 'user.avatar', $now);

        $newUser = $this->service->updateAvatar($user, $storedFile);

        self::assertNotNull($newUser->getAvatar());
        self::assertEquals($this->userId, $newUser->getId());
        self::assertEquals($this->identityId, $newUser->getIdentityId());
        self::assertEquals($createdAt, $newUser->getCreatedAt());
        self::assertNotEquals($createdAt, $newUser->getUpdatedAt());
        self::assertEquals('stored_filepath', $newUser->getAvatar()->getPath());
        self::assertEquals('stored_filename', $newUser->getAvatar()->getFilename());
        self::assertEquals('stored_mimetype', $newUser->getAvatar()->getMimeType());
        self::assertEquals('stored_extension', $newUser->getAvatar()->getExtension());
        self::assertEquals('user.avatar', $newUser->getAvatar()->getType());
        self::assertEquals($now, $newUser->getAvatar()->getCreatedAt());
    }

    #[Test]
    public function shouldDeleteAvatar(): void
    {
        $oldFileId = EntityId::generate();
        $fileCreatedAt = new \DateTimeImmutable('2025-11-06T12:00:00');
        $oldFile = new StoredFile($oldFileId, 'old_stored_filepath', 'old_stored_filename', 'old_stored_mimetype', 'old_stored_extension', 'user.avatar', $fileCreatedAt);
        $createdAt = new \DateTimeImmutable('2025-11-05T12:00:00');
        $user = new User($this->userId, $this->identityId, $createdAt, $createdAt, $oldFile);
        $user->setAvatar($oldFile);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $newUser = $this->service->updateAvatar($user, null);

        self::assertNull($newUser->getAvatar());
        self::assertEquals($this->userId, $newUser->getId());
        self::assertEquals($this->identityId, $newUser->getIdentityId());
        self::assertEquals($createdAt, $newUser->getCreatedAt());
        self::assertNotEquals($createdAt, $newUser->getUpdatedAt());
    }
}
