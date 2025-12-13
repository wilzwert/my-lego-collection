<?php

namespace App\Tests\CollectionManagement\Unit\Application\Handler;

use App\CollectionManagement\Application\Command\AddUserSetCommand;
use App\CollectionManagement\Application\Handler\AddUserSetHandler;
use App\CollectionManagement\Domain\Event\SetCreatedEvent;
use App\CollectionManagement\Domain\Event\UserSetCreatedEvent;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\Local\UserSetStatus;
use App\CollectionManagement\Domain\Port\Driven\RetrieveUserId;
use App\CollectionManagement\Domain\Port\Driven\SetRepository;
use App\CollectionManagement\Domain\Port\Driven\UserSetRepository;
use App\CollectionManagement\Domain\Service\SetService;
use App\CollectionManagement\Domain\Service\UserSetService;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Port\Driven\EventBus;
use App\Shared\Domain\Port\Driven\TransactionProvider;
use App\Tests\CollectionManagement\Utilities\CollectionManagementTestsUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class AddUserSetHandlerTest extends TestCase
{
    private RetrieveUserId&MockObject $retrieveUser;
    private SetRepository&MockObject $localSetRepository;
    private SetService&MockObject $setService;
    private UserSetService&MockObject $userSetService;
    private UserSetRepository&MockObject $userSetRepository;
    private TransactionProvider&MockObject $transactionProvider;
    private EventBus&MockObject $eventBus;


    private AddUserSetHandler $addUserSetHandler;

    protected function setUp(): void
    {
        $this->retrieveUser = $this->createMock(RetrieveUserId::class);
        $this->localSetRepository = $this->createMock(SetRepository::class);
        $this->setService = $this->createMock(SetService::class);
        $this->userSetService = $this->createMock(UserSetService::class);
        $this->userSetRepository = $this->createMock(UserSetRepository::class);
        $this->transactionProvider = $this->createMock(TransactionProvider::class);
        $this->eventBus = $this->createMock(EventBus::class);

        $this->addUserSetHandler = new AddUserSetHandler(
            $this->retrieveUser,
            $this->localSetRepository,
            $this->setService,
            $this->userSetService,
            $this->userSetRepository,
            $this->transactionProvider,
            $this->eventBus
        );
    }

    #[Test]
    public function whenUserIdNotFound_thenShouldThrowEntityNotFoundException(): void
    {
        $identityId = EntityId::generate();
        $externalSetId = 'externalSetId';
        $this->retrieveUser
            ->expects($this->once())
            ->method('getUserId')
            ->with($identityId)
            ->willReturn(null);

        $this->setService->expects($this->never())->method('createSet');
        $this->localSetRepository->expects($this->never())->method('findByExternalId');
        $this->localSetRepository->expects($this->never())->method('save');
        $this->eventBus->expects($this->never())->method('dispatchAll');
        $this->userSetService->expects($this->never())->method('createUserSet');
        $this->userSetRepository->expects($this->never())->method('save');
        $this->transactionProvider->expects($this->never())->method('transactional');


        self::expectException(EntityNotFoundException::class);
        $createdUserSet = ($this->addUserSetHandler)(new AddUserSetCommand($externalSetId, $identityId, UserSetStatus::BUILT));
    }


    #[Test]
    public function shouldHandleAddUserSetWithNewSet(): void
    {
        $externalSetId = 'externalSetId';
        $identityId = EntityId::generate();
        $userId = EntityId::generate();
        $localSet = Set::create(
            $externalSetId,
            'legoId',
            'Lego set',
            200,
            'image.png',
            2005
        );

        $userSet = UserSet::create($userId, $localSet->getId(), UserSetStatus::BUILT);

        $this->retrieveUser
            ->expects($this->once())
            ->method('getUserId')
            ->with($identityId)
            ->willReturn($userId);

        $this->localSetRepository
            ->expects($this->once())
            ->method('findByExternalId')
            ->with($externalSetId)
            ->willReturn(null);

        $this->setService
            ->expects($this->once())
            ->method('createSet')
            ->with($externalSetId)
            ->willReturn($localSet);

        $this->localSetRepository
            ->expects($this->once())
            ->method('save')
            ->with($localSet);

        $this->userSetService
            ->expects($this->once())
            ->method('createUserSet')
            ->with($userId, $localSet)
            ->willReturn($userSet);

        $this->userSetRepository
            ->expects($this->once())
            ->method('save')
            ->with($userSet);

        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
                // simulate transaction -> just execute callback
                fn (callable $callback) => $callback()
            );

        $this->eventBus
            ->expects($this->exactly(2))
            ->method('dispatchAll')
            ->with(
                $this->callback(function ($arg) use (&$dispatchedEntities) {
                    $dispatchedEntities[] = $arg;
                    return true;
                })
            );

        $createdUserSet = ($this->addUserSetHandler)(new AddUserSetCommand($externalSetId, $identityId, UserSetStatus::BUILT));
        self::assertNotNull($userSet);
        self::assertSame($createdUserSet, $userSet);
        self::assertSame($localSet, $dispatchedEntities[0]);
        self::assertSame($createdUserSet, $dispatchedEntities[1]);
        self::assertEquals($userId, $userSet->getUserId());

        $events = $dispatchedEntities[0]->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(SetCreatedEvent::class, $events[0]);

        $events = $dispatchedEntities[1]->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserSetCreatedEvent::class, $events[0]);
    }

    #[Test]
    public function shouldHandleAddUserSetWithExistingSet(): void
    {
        $externalSetId = 'externalSetId';
        $identityId = EntityId::generate();
        $userId = EntityId::generate();
        $localSet = CollectionManagementTestsUtility::generateKnownSet();

        $userSet = UserSet::create($userId, $localSet->getId(), UserSetStatus::OWNED);

        $this->retrieveUser
            ->expects($this->once())
            ->method('getUserId')
            ->with($identityId)
            ->willReturn($userId);

        $this->localSetRepository
            ->expects($this->once())
            ->method('findByExternalId')
            ->with($externalSetId)
            ->willReturn($localSet);

        $this->userSetService
            ->expects($this->once())
            ->method('createUserSet')
            ->with($userId, $localSet)
            ->willReturn($userSet);

        $this->transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
                // simulate transaction -> just execute callback
                fn (callable $callback) => $callback()
            );

        $this->userSetRepository->expects($this->once())->method('save')->with($userSet);

        $this->eventBus
            ->expects($this->once())
            ->method('dispatchAll')
            ->with(
                $this->callback(function ($arg) use (&$dispatchedUserSet) {
                    $dispatchedUserSet = $arg;
                    return true;
                })
            );

        $this->setService->expects($this->never())->method('createSet');

        $createdUserSet = ($this->addUserSetHandler)(new AddUserSetCommand($externalSetId, $identityId, UserSetStatus::OWNED));
        self::assertNotNull($userSet);
        self::assertSame($createdUserSet, $userSet);
        self::assertSame($createdUserSet, $dispatchedUserSet);
        self::assertEquals($userId, $userSet->getUserId());
        $events = $dispatchedUserSet->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(UserSetCreatedEvent::class, $events[0]);
    }
}
