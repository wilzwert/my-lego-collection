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
    }

    #[Test]
    public function shouldSaveNotificationLogs(): void
    {
        $container = self::getContainer();

        // get the real dispatcher to add a test sender
        /** @var NotificationDispatcherAdapter $dispatcher */
        $dispatcher = $container->get(NotificationDispatcherAdapter::class);
        $errorSender = new ErrorSender();
        $dispatcher->addSender($errorSender);
        $emailSender = $container->get(EmailSender::class);
        $smsSender = $container->get(SmsSender::class);

        $command = NotificationTestsUtility::generateSendWelcomeNotificationCommand(
            EntityId::fromString(TestData::EXISTING_IDENTITY_USER1_ID)
        );

        /** @var NotificationCommandHandler $handler */
        $handler = $container->get(NotificationCommandHandler::class);
        $handler($command);

        // check 3 NotificationLogs have been saved, including one in error
        /** @var NotificationLogRepository $repository */
        $repository = $container->get(NotificationLogRepository::class);
        /** @var NotificationLog[] $notificationLogs */
        $notificationLogs = $repository->findByMessageId($command->id());

        self::assertCount(3, $notificationLogs);
        self::assertTrue(
            array_any(
                $notificationLogs,
                fn(DoctrineNotificationLog $notificationLog) => $notificationLog->getSender() === $errorSender->getName() && $notificationLog->getStatus() === NotificationStatus::ERROR
            )
        );
        self::assertTrue(
            array_any(
                $notificationLogs,
                fn(DoctrineNotificationLog $notificationLog) => $notificationLog->getSender() === $emailSender->getName() && $notificationLog->getStatus() === NotificationStatus::SENT
            )
        );
        self::assertTrue(
            array_any(
                $notificationLogs,
                fn(DoctrineNotificationLog $notificationLog) => $notificationLog->getSender() === $smsSender->getName() && $notificationLog->getStatus() === NotificationStatus::SENT
            )
        );
    }
}
