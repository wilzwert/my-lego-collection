<?php

namespace App\Tests\CollectionManagement\Unit\Domain\Application\Handler;

use App\CollectionManagement\Application\Command\AddUserSetCommand;
use App\CollectionManagement\Application\Handler\AddUserSetHandler;
use App\CollectionManagement\Domain\Service\RetrieveUserId;
use App\Shared\Domain\Model\EntityId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class AddUserSetHandlerTest extends TestCase
{
    private RetrieveUserId&MockObject $retrieveUser;

    private AddUserSetHandler $addUserSetHandler;

    protected function setUp(): void
    {
        $this->retrieveUser = $this->createMock(RetrieveUserId::class);
        $this->addUserSetHandler = new AddUserSetHandler($this->retrieveUser);
    }

    #[Test]
    public function shouldHandleAddUserSet(): void
    {
        $identityId = EntityId::generate();
        $userId = EntityId::generate();

        $this->retrieveUser
            ->expects($this->once())
            ->method('getUserId')
            ->with($identityId)
            ->willReturn($userId);

        $userSet = ($this->addUserSetHandler)(new AddUserSetCommand('externalSetId', $identityId));
        self::assertNotNull($userSet);
        self::assertEquals($userId, $userSet->getUserId());
    }

}
