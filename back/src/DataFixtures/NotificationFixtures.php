<?php

namespace App\DataFixtures;

use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Model\NotificationType;
use App\Notification\Infrastructure\Persistence\Doctrine\Entity\DoctrineNotificationLog;
use App\Shared\Domain\Model\EntityId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class NotificationFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        // successful email notification log
        $notificationLog = new DoctrineNotificationLog()->fromDomain(
            new NotificationLog(
                id: EntityId::fromString(TestData::IDENTITY1_SENT_EMAIL_WELCOME_NOTIFICATION_LOG_ID),
                identityId: EntityId::fromString(TestData::IDENTITY1_ID),
                userId: null,
                messageId: EntityId::fromString(TestData::IDENTITY1_SENT_EMAIL_WELCOME_MESSAGE_ID),
                type: NotificationType::WELCOME,
                sender: 'email',
                status: NotificationStatus::SENT,
                statusMessage: 'Sent email',
                createdAt: \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-01 14:45:00')
            )
        );

        $manager->persist($notificationLog);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
