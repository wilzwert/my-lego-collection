<?php

namespace App\Tests\Notification\Application\Service;

use App\Notification\Application\Service\DefaultNotificationFactory;
use App\Notification\Domain\Model\IdentityInfo;
use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\WelcomeNotification;
use App\Notification\Application\Ports\Driven\RetrieveIdentityInfo;
use App\Tests\Utilities\TestData;
use MyLegoCollection\SharedEvent\Command\Command;
use MyLegoCollection\SharedEvent\Command\SendWelcomeNotificationCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class DefaultNotificationFactoryTest extends TestCase
{
    private RetrieveIdentityInfo&MockObject $retrieveIdentityInfoMock;

    private DefaultNotificationFactory $factory;

    protected function setUp(): void
    {
        $this->retrieveIdentityInfoMock = $this->createMock(RetrieveIdentityInfo::class);
        $this->factory = new DefaultNotificationFactory($this->retrieveIdentityInfoMock);
    }

    #[Test]
    public function shouldCreateWelcomeNotification(): void
    {
        $command = new SendWelcomeNotificationCommand(TestData::EXISTING_IDENTITY_ID, 'validationToken');
        $info = new IdentityInfo(TestData::EXISTING_IDENTITY_ID, 'test@example.com');

        $this->retrieveIdentityInfoMock
            ->expects(self::once())
            ->method('getIdentityInfoFromId')
            ->willReturn($info);

        $notification = $this->factory->createNotification($command);
        self::assertInstanceOf(WelcomeNotification::class, $notification);
        self::assertEquals(TestData::EXISTING_IDENTITY_ID, $notification->getIdentityInfo()->getIdentityId());
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

        self::expectException(\InvalidArgumentException::class);
        $notification = $this->factory->createNotification($command);

    }

}
