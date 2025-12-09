<?php

namespace App\Tests\Notification\Integration\Infrastructure\EventHandler;

use App\DataFixtures\TestData;
use App\Notification\Application\Handler\NotificationCommandHandler;
use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Port\Driven\NotificationLogRepository;
use App\Notification\Infrastructure\Adapter\NotificationDispatcherAdapter;
use App\Notification\Infrastructure\EventHandler\SendWelcomeNotificationCommandHandler;
use App\Notification\Infrastructure\Sender\EmailSender;
use App\Shared\Domain\Model\EntityId;
use App\Tests\Notification\Integration\Infrastructure\Sender\ErrorSender;
use App\Tests\Notification\Utilities\NotificationTestsUtility;
use MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class SendWelcomeNotificationCommandHandlerIT extends KernelTestCase
{

    #[Test]
    public function shouldSendWelcomeNotificationCommand(): void
    {
        $container = self::getContainer();
        /** @var SendWelcomeNotificationCommandHandler $handler */
        $handler = $container->get(SendWelcomeNotificationCommandHandler::class);

        // user2 has no sent notification yet
        $identityId = TestData::IDENTITY2_ID;
        $command = new SendWelcomeNotificationCommand($identityId, 'validation-token');
        $handler($command);

        self::assertEmailCount(1);

        $email = $this->getMailerMessage();
        self::assertEmailHtmlBodyContains($email, 'Welcome');
        self::assertEmailHtmlBodyContains($email, 'user2');
        self::assertEmailTextBodyContains($email, 'Welcome');
        self::assertEmailTextBodyContains($email, 'user2');
        self::assertEmailAddressContains($email, 'To', 'user2@test.com');

        // check a NotificationLogs have been saved
        $repository = $container->get(NotificationLogRepository::class);
        self::assertCount(1, $repository->findByMessageIdAndStatus($command->id(), NotificationStatus::SENT));

        self::assertNotificationCount(0);
    }

    #[Test]
    public function whenWelcomeNotificationCommandAlreadySent_thenShouldNotResend(): void
    {
        $container = self::getContainer();
        /** @var SendWelcomeNotificationCommandHandler $handler */
        $handler = $container->get(SendWelcomeNotificationCommandHandler::class);

        // a command sent with a message already handled and sent by sms only (see fixtures)
        $command = NotificationTestsUtility::generateSentSendWelcomeNotificationCommand();

        $handler($command);

        self::assertEmailCount(0);

        // nothing should have been sent by symfony notifier
        self::assertNotificationCount(0);

        // check only one NotificationLog has been saved (for the email), but we know there already was one (see fixtures)
        $repository = $container->get(NotificationLogRepository::class);
        self::assertCount(1, $repository->findByMessageIdAndStatus($command->id(), NotificationStatus::SENT));

    }

    #[Test]
    public function shouldDispatchWelcomeMessageAndSaveNotificationLogs(): void
    {
        $container = self::getContainer();

        // get the real dispatcher to add a test sender
        /** @var NotificationDispatcherAdapter $dispatcher */
        $dispatcher = $container->get(NotificationDispatcherAdapter::class);
        $errorSender = new ErrorSender();
        $dispatcher->addSender($errorSender);
        $emailSender = $container->get(EmailSender::class);

        $command = NotificationTestsUtility::generateSendWelcomeNotificationCommand(
            EntityId::fromString(TestData::IDENTITY2_ID)
        );

        /** @var NotificationCommandHandler $handler */
        $handler = $container->get(NotificationCommandHandler::class);
        $handler($command);

        self::assertEmailCount(1);
        self::assertNotificationCount(0);

        // check 3 NotificationLogs have been saved, including one in error (sms)
        /** @var NotificationLogRepository $repository */
        $repository = $container->get(NotificationLogRepository::class);
        $notificationLogs = $repository->findByMessageId($command->id());

        self::assertCount(2, $notificationLogs);
        self::assertTrue(
            array_any(
                $notificationLogs,
                fn(NotificationLog $notificationLog) => $notificationLog->getSender() === $errorSender->getName() && $notificationLog->getStatus() === NotificationStatus::ERROR
            )
        );
        self::assertTrue(
            array_any(
                $notificationLogs,
                fn(NotificationLog $notificationLog) => $notificationLog->getSender() === $emailSender->getName() && $notificationLog->getStatus() === NotificationStatus::SENT
            )
        );
    }
}
