<?php

namespace App\Tests\Notification\Integration\Infrastructure\EventHandler;

use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Port\Driven\NotificationLogRepository;
use App\Notification\Infrastructure\EventHandler\SendWelcomeNotificationCommandHandler;
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
        $identityId = TestData::EXISTING_IDENTITY_ID;
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
    }

    #[Test]
    public function whenWelcomeNotificationCommandAlreadySent_thenShouldNotResend(): void
    {
        $container = self::getContainer();
        /** @var SendWelcomeNotificationCommandHandler $handler */
        $handler = $container->get(SendWelcomeNotificationCommandHandler::class);

        // a command sent with a message already handled and sent by sms only
        $identityId = TestData::EXISTING_IDENTITY_ID;
        // FIXME : as of now, the only I found to actually create a command
        $command = unserialize('O:71:"MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand":5:{s:44:" MyLegoCollection\SharedContracts\Message id";s:36:"c1c1c1c1-c1c1-41c1-8c1c-c1c1c1c1c1c1";s:50:" MyLegoCollection\SharedContracts\Message metadata";a:1:{s:11:"occurred_at";s:25:"2025-12-05T18:41:40+01:00";}s:46:" MyLegoCollection\SharedContracts\Message type";s:20:"welcome.notification";s:83:" MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand identityId";s:36:"a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1";s:88:" MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand validationToken";s:16:"validation-token";}');
        var_dump($command);
        die();
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
    }
}
