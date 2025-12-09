<?php

namespace App\Tests\Notification\Unit\Application\Service;

use App\DataFixtures\TestData;
use App\Notification\Domain\Model\IdentityInfo;
use App\Notification\Domain\Model\WelcomeNotification;
use App\Notification\Domain\Port\Driven\RetrieveIdentityInfo;
use MyLegoCollection\SharedContracts\Command\Command;
use MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class DefaultNotificationFactoryTest extends TestCase
{
    private RetrieveIdentityInfo&MockObject $retrieveIdentityInfoMock;

    private \App\Notification\Domain\Service\DefaultNotificationFactory $factory;

    protected function setUp(): void
    {
        $this->retrieveIdentityInfoMock = $this->createMock(RetrieveIdentityInfo::class);
        $this->factory = new \App\Notification\Domain\Service\DefaultNotificationFactory($this->retrieveIdentityInfoMock);
    }

    #[Test]
    public function shouldCreateWelcomeNotification(): void
    {
        $command = new SendWelcomeNotificationCommand(TestData::IDENTITY1_ID, 'validationToken');
        $info = new IdentityInfo(
            TestData::IDENTITY1_ID,
            'test@example.com',
        'username'
        );

        $this->retrieveIdentityInfoMock
            ->expects($this->once())
            ->method('getIdentityInfoFromId')
            ->willReturn($info);

        $notification = $this->factory->createNotification($command);
        self::assertInstanceOf(WelcomeNotification::class, $notification);
        self::assertEquals(TestData::IDENTITY1_ID, $notification->getIdentityInfo()->getIdentityId());
        self::assertEquals('test@example.com', $notification->getIdentityInfo()->getEmail());
        self::assertEquals('validationToken', $notification->getPayload()['validationToken']);
    }

    #[Test]
    public function whenUnknownMessage_thenShouldThrowInvalidArgumentException(): void
    {
        $command = new class extends Command {
            public function __construct()
            {
                parent::__construct('unknown');
            }
        };

        $this->retrieveIdentityInfoMock
            ->expects($this->never())
            ->method('getIdentityInfoFromId');

        self::expectException(\InvalidArgumentException::class);
        $notification = $this->factory->createNotification($command);

    }

}
