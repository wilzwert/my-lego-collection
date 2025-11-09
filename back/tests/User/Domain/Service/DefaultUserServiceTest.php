<?php

namespace App\Tests\User\Domain\Service;

use App\Shared\Domain\Model\File;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\TransactionProvider;
use App\Shared\Domain\Service\UploadedFileStorageService;
use App\User\Application\Command\CreateUserCommand;
use App\User\Application\Command\DeleteAvatarCommand;
use App\User\Application\Command\UpdateAvatarCommand;
use App\User\Domain\Model\User;
use App\User\Domain\Repository\UserRepository;
use App\User\Domain\Service\DefaultUserService;
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
    private UploadedFileStorageService $uploadedFileStorage;
    private DefaultUserService $service;

    protected function setUp(): void
    {
        $this->identityId = EntityId::fromString('87654321-e89b-42d3-a456-426614174000');
        $this->userId = EntityId::fromString('123e4567-e89b-42d3-a456-426614174000');

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);
        $this->uploadedFileStorage = $this->createMock(UploadedFileStorageService::class);

        $this->service = new DefaultUserService(
            $this->userRepository,
            $this->transactionProvider,
            $this->uploadedFileStorage
        );
    }

    #[Test]
    public function shouldCreateUserWithinTransaction(): void
    {
        $command = new CreateUserCommand($this->identityId->__toString());
        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with($command->identityId)
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

        $result = $this->service->createUser($command);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($this->identityId, $result->getIdentityId());
        $this->assertNull($result->getAvatar());
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

        $this->assertSame($expectedUser, $result);
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

        $this->assertSame($expectedUser, $result);
    }

    #[Test]
    public function shouldUpdateAvatar(): void
    {

        $createdAt = new \DateTimeImmutable('2025-11-05T12:00:00');
        $user = new User($this->userId, $this->identityId, $createdAt, $createdAt);

        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with($this->identityId)
            ->willReturn($user);

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

        $this->uploadedFileStorage
            ->expects($this->never())
            ->method('delete');

        $fileId = EntityId::generate();
        $now = new \DateTimeImmutable();
        $file = new File($fileId, 'stored_filepath', 'stored_filename', 'stored_mimetype', 'stored_extension', 'user.avatar', $now);

        $this->uploadedFileStorage
            ->expects($this->once())
            ->method('upload')
            ->with('filepath', 'filename', 'user.avatar')
            ->willReturn($file);

        $newUser = $this->service->updateAvatar(new UpdateAvatarCommand($this->identityId, 'filepath', 'filename'));

        $this->assertNotNull($newUser->getAvatar());
        $this->assertEquals($this->userId, $newUser->getId());
        $this->assertEquals($this->identityId, $newUser->getIdentityId());
        $this->assertEquals($createdAt, $newUser->getCreatedAt());
        $this->assertNotEquals($createdAt, $newUser->getUpdatedAt());
        $this->assertEquals('stored_filepath', $newUser->getAvatar()->getPath());
        $this->assertEquals('stored_filename', $newUser->getAvatar()->getFilename());
        $this->assertEquals('stored_mimetype', $newUser->getAvatar()->getMimeType());
        $this->assertEquals('stored_extension', $newUser->getAvatar()->getExtension());
        $this->assertEquals('user.avatar', $newUser->getAvatar()->getType());
        $this->assertEquals($now, $newUser->getAvatar()->getCreatedAt());



    }

    #[Test]
    public function shouldDeleteAndUpdateAvatar(): void
    {

        $oldFileId = EntityId::generate();
        $fileCreatedAt = new \DateTimeImmutable('2025-11-06T12:00:00');
        $oldFile = new File($oldFileId, 'old_stored_filepath', 'old_stored_filename', 'old_stored_mimetype', 'old_stored_extension', 'user.avatar', $fileCreatedAt);
        $createdAt = new \DateTimeImmutable('2025-11-05T12:00:00');
        $user = new User($this->userId, $this->identityId, $createdAt, $createdAt, $oldFile);

        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with($this->identityId)
            ->willReturn($user);

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

        $this->uploadedFileStorage
            ->expects($this->once())
            ->method('delete')
            ->with($oldFile);

        $fileId = EntityId::generate();
        $now = new \DateTimeImmutable();
        $file = new File($fileId, 'stored_filepath', 'stored_filename', 'stored_mimetype', 'stored_extension', 'user.avatar', $now);

        $this->uploadedFileStorage
            ->expects($this->once())
            ->method('upload')
            ->with('filepath', 'filename', 'user.avatar')
            ->willReturn($file);

        $newUser = $this->service->updateAvatar(new UpdateAvatarCommand($this->identityId, 'filepath', 'filename'));

        $this->assertNotNull($newUser->getAvatar());
        $this->assertEquals($this->userId, $newUser->getId());
        $this->assertEquals($this->identityId, $newUser->getIdentityId());
        $this->assertEquals($createdAt, $newUser->getCreatedAt());
        $this->assertNotEquals($createdAt, $newUser->getUpdatedAt());
        $this->assertEquals('stored_filepath', $newUser->getAvatar()->getPath());
        $this->assertEquals('stored_filename', $newUser->getAvatar()->getFilename());
        $this->assertEquals('stored_mimetype', $newUser->getAvatar()->getMimeType());
        $this->assertEquals('stored_extension', $newUser->getAvatar()->getExtension());
        $this->assertEquals('user.avatar', $newUser->getAvatar()->getType());
        $this->assertEquals($now, $newUser->getAvatar()->getCreatedAt());
    }

    #[Test]
    public function shouldDeleteAvatar(): void
    {
        $oldFileId = EntityId::generate();
        $fileCreatedAt = new \DateTimeImmutable('2025-11-06T12:00:00');
        $oldFile = new File($oldFileId, 'old_stored_filepath', 'old_stored_filename', 'old_stored_mimetype', 'old_stored_extension', 'user.avatar', $fileCreatedAt);
        $createdAt = new \DateTimeImmutable('2025-11-05T12:00:00');
        $user = new User($this->userId, $this->identityId, $createdAt, $createdAt, $oldFile);

        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with($this->identityId)
            ->willReturn($user);

        // simulate TransactionProvider behavior
        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
            // simulate transaction -> just execute callback
                fn (callable $callback) => $callback()
            );

        $this->uploadedFileStorage
            ->expects($this->once())
            ->method('delete')
            ->with($oldFile);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $newUser = $this->service->deleteAvatar(new DeleteAvatarCommand($this->identityId));

        $this->assertNull($newUser->getAvatar());
        $this->assertEquals($this->userId, $newUser->getId());
        $this->assertEquals($this->identityId, $newUser->getIdentityId());
        $this->assertEquals($createdAt, $newUser->getCreatedAt());
        $this->assertNotEquals($createdAt, $newUser->getUpdatedAt());
    }
}
