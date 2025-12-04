<?php

namespace App\Tests\Notification\Integration\Infrastructure\EventHandler;

use App\Notification\Domain\Ports\Driven\NotificationLogRepository;
use App\Notification\Infrastructure\EventHandler\SendWelcomeNotificationCommandHandler;
use App\Tests\Utilities\TestData;
use MyLegoCollection\SharedEvent\Command\SendWelcomeNotificationCommand;
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

        // as of now, identity retrieval in the Notification module always returns
        // new IdentityInfo('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1', 'test@example.com', 'username');
        $identityId = TestData::EXISTING_IDENTITY_ID;
        $command = new SendWelcomeNotificationCommand($identityId, 'validation-token');
        $handler($command);

        self::assertEmailCount(1);

        $email = $this->getMailerMessage();
        self::assertEmailHtmlBodyContains($email, 'Welcome');
        self::assertEmailHtmlBodyContains($email, 'username');
        self::assertEmailTextBodyContains($email, 'Welcome');
        self::assertEmailTextBodyContains($email, 'username');
        self::assertEmailAddressContains($email, 'To', 'test@example.com');

        // check a NotificationLog has been saved
        $repository = $container->get(NotificationLogRepository::class);
        self::assertCount(1, $repository->findAll());


    }
}
