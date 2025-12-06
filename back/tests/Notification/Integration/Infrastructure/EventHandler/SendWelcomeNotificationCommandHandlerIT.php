<?php

namespace App\Tests\Notification\Integration\Infrastructure\EventHandler;

use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Port\Driven\NotificationLogRepository;
use App\Notification\Infrastructure\EventHandler\SendWelcomeNotificationCommandHandler;
use App\Notification\Infrastructure\Sender\SmsSender;
use App\Tests\Notification\Utilities\NotificationTestsUtility;
use App\Tests\Utilities\TestData;
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

        // TODO : use real data now that the cross modules data retrieval is implemented
        $identityId = TestData::EXISTING_IDENTITY_USER1_ID;
        $command = new SendWelcomeNotificationCommand($identityId, 'validation-token');
        $handler($command);

        self::assertEmailCount(1);

        $email = $this->getMailerMessage();
        self::assertEmailHtmlBodyContains($email, 'Welcome');
        self::assertEmailHtmlBodyContains($email, 'user1');
        self::assertEmailTextBodyContains($email, 'Welcome');
        self::assertEmailTextBodyContains($email, 'user1');
        self::assertEmailAddressContains($email, 'To', 'user1@test.com');

        // check a NotificationLogs have been saved
        $repository = $container->get(NotificationLogRepository::class);
        self::assertCount(2, $repository->findByMessageIdAndStatus($command->id(), NotificationStatus::SENT));

        // also, an SMS should have been sent
        self::assertEquals(1, $container->get(SmsSender::class)->getSmsSentCount());
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

        self::assertEmailCount(1);

        $email = $this->getMailerMessage();
        self::assertEmailHtmlBodyContains($email, 'Welcome');
        self::assertEmailHtmlBodyContains($email, 'user1');
        self::assertEmailTextBodyContains($email, 'Welcome');
        self::assertEmailTextBodyContains($email, 'user1');
        self::assertEmailAddressContains($email, 'To', 'user1@test.com');

        // check only one NotificationLog has been saved (for the email), but we know there already was one (see fixtures)
        $repository = $container->get(NotificationLogRepository::class);
        self::assertCount(2, $repository->findByMessageIdAndStatus($command->id(), NotificationStatus::SENT));
        // also, no SMS should have been sent
        self::assertEquals(0, $container->get(SmsSender::class)->getSmsSentCount());
    }
}
