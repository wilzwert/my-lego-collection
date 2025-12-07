<?php

namespace App\Tests\Notification\Integration\Application;

use App\Notification\Application\Handler\NotificationCommandHandler;
use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Port\Driven\NotificationLogRepository;
use App\Notification\Infrastructure\Adapter\NotificationDispatcherAdapter;
use App\Notification\Infrastructure\Persistence\Doctrine\Entity\DoctrineNotificationLog;
use App\Notification\Infrastructure\Sender\EmailSender;
use App\Notification\Infrastructure\Sender\SmsSender;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Tests\Notification\Integration\Infrastructure\Sender\ErrorSender;
use App\Tests\Notification\Utilities\NotificationTestsUtility;
use App\Tests\Utilities\TestData;
use MyLegoCollection\SharedContracts\Command\Command;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Notifier\Message\SmsMessage;

/**
 * @author Wilhelm Zwertvaegher
 */
class NotificationCommandHandlerIT extends KernelTestCase
{

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    #[Test]
    public function whenCommandTypeUnknown_thenShouldThrowInvalidArgumentException(): void
    {
        $command = new class extends Command {
            public function __construct()
            {
                parent::__construct("unknown");
            }
        };

        $container = self::getContainer();

        /** @var NotificationCommandHandler $handler */
        $handler = $container->get(NotificationCommandHandler::class);

        self::expectException(\InvalidArgumentException::class);
        $handler($command);
    }

    /**
     * All Messages MUST be linked to at least an existing Identity
     * @return void
     */
    #[Test]
    public function whenIdentityNotFound_thenShouldThrowEntityNotFoundException(): void
    {
        // creating a random command for an unknown identity
        $command = NotificationTestsUtility::generateSendWelcomeNotificationCommand();

        $container = self::getContainer();

        /** @var NotificationCommandHandler $handler */
        $handler = $container->get(NotificationCommandHandler::class);

        self::expectException(EntityNotFoundException::class);
        $handler($command);

        self::assertEmailCount(0);

        // check no NotificationLog has been saved
        /** @var NotificationLogRepository $repository */
        $repository = $container->get(NotificationLogRepository::class);
        /** @var NotificationLog[] $notificationLogs */
        $notificationLogs = $repository->findByMessageId($command->id());
        self::assertCount(0, $notificationLogs);

        self::assertEmailCount(0);
        self::assertNotificationCount(0);
    }
}
