<?php

namespace App\Tests\CollectionManagement\Unit\Application\Handler;

use App\CollectionManagement\Application\Command\AddUserSetCommand;
use App\CollectionManagement\Application\Handler\AddUserSetHandler;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\CollectionManagement\Domain\Port\Driven\LocalSetRepository;
use App\CollectionManagement\Domain\Service\RetrieveUserId;
use App\CollectionManagement\Domain\Service\SetService;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping\Entity;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class AddUserSetHandlerTest extends TestCase
{
    private RetrieveUserId&MockObject $retrieveUser;
    private LocalSetRepository&MockObject $localSetRepository;
    private SetService&MockObject $setService;

    private AddUserSetHandler $addUserSetHandler;

    protected function setUp(): void
    {
        $this->retrieveUser = $this->createMock(RetrieveUserId::class);
        $this->localSetRepository = $this->createMock(LocalSetRepository::class);
        $this->localSetRepository = $this->createMock(LocalSetRepository::class);
        $this->setService = $this->createMock(SetService::class);

        $this->addUserSetHandler = new AddUserSetHandler($this->retrieveUser, $this->localSetRepository, $this->setService);
    }

    #[Test]
    public function shouldHandleAddUserSet(): void
    {
        $externalSetId = 'externalSetId';
        $identityId = EntityId::generate();
        $userId = EntityId::generate();
        $localSet = new Set(
            EntityId::generate(),
            $externalSetId,
            'legoId',
            'Lego set',
            200,
            'image.png',
            2005,
            SetCreationStatus::COMPLETED
        );

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

        $this->setService
            ->expects($this->never())
            ->method('createSet');

        $userSet = ($this->addUserSetHandler)(new AddUserSetCommand($externalSetId, $identityId));
        self::assertNotNull($userSet);
        self::assertEquals($userId, $userSet->getUserId());
    }

}
