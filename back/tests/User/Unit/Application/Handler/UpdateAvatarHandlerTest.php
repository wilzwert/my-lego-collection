<?php

namespace App\Tests\User\Unit\Application\Handler;

use App\DataFixtures\TestData;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\TempFile;
use App\Shared\Domain\Port\Driven\EventBus;
use App\Shared\Domain\Port\Driven\TransactionProvider;
use App\Shared\Domain\Service\StoredFileService;
use App\Tests\User\Utilities\UserTestsUtility;
use App\User\Application\Command\UpdateAvatarCommand;
use App\User\Application\Handler\UpdateAvatarHandler;
use App\User\Domain\Model\User;
use App\User\Domain\Port\Driven\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class UpdateAvatarHandlerTest extends TestCase
{

    private readonly TransactionProvider&MockObject $transactionProvider;
    private readonly StoredFileService&MockObject $storedFileService;
    private readonly UserRepository&MockObject $userRepository;
    private readonly EventBus&MockObject $eventBus;
    private readonly UpdateAvatarHandler $underTest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionProvider = $this->createMock(TransactionProvider::class);
        $this->storedFileService = $this->createMock(StoredFileService::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->eventBus = $this->createMock(EventBus::class);
        $this->underTest = new UpdateAvatarHandler(
            $this->transactionProvider,
            $this->storedFileService,
            $this->userRepository,
            $this->eventBus
        );
    }

    #[Test]
    public function shouldUpdateAvatar(): void
    {
        $user = UserTestsUtility::generateUser(
            avatar: new StoredFile(
                id: EntityId::generate(),
                path: 'path/to/stored/file',
                filename: 'old.jpg',
                mimeType: 'image/jpeg',
                extension: 'jpg',
                type: 'user.avatar',
                createdAt: new \DateTimeImmutable('2025-11-07 13:10:00')
            )
        );

        $command = new UpdateAvatarCommand(
            TestData::IDENTITY1_ID,
            new TempFile(
                'path/to/file',
                'file.pdf',
                'application/pdf',
                'pdf'
            )
        );

        $newStoredFile = new StoredFile(
            id: EntityId::generate(),
            path: 'path/to/new/file',
            filename: 'new.jpg',
            mimeType: 'image/jpeg',
            extension: 'jpg',
            type: 'user.avatar',
            createdAt: new \DateTimeImmutable('2025-11-12 12:00:00')
        );

        $this->userRepository
            ->expects($this->once())
            ->method('findByIdentityId')
            ->with(TestData::IDENTITY1_ID)
            ->willReturn($user);

        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
                // simulate transaction -> just execute callback
                fn (callable $callback) => $callback()
            );

        $this->storedFileService
            ->expects($this->once())
            ->method('replace')
            ->with($user->getAvatar(), $command->tempFile, 'user.avatar')
            ->willReturn($newStoredFile);

        $this->eventBus
            ->expects($this->once())
            ->method('dispatchAll')
            ->with($this->callback(function (User $u) use (&$eventsUser) {
                $eventsUser = $u;
                return true;
            }));

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $u) use (&$savedUser) {
                $savedUser = $u;
                return true;
            }));

        $updatedUser = ($this->underTest)($command);

        self::assertNotSame($user, $updatedUser);
        self::assertSame($updatedUser, $savedUser);
        self::assertEquals($newStoredFile, $updatedUser->getAvatar());
    }
}
