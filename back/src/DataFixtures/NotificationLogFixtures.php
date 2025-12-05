<?php

namespace App\DataFixtures;

use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Model\NotificationType;
use App\Notification\Infrastructure\Persistence\Doctrine\Entity\DoctrineNotificationLog;
use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;
use App\User\Infrastructure\Persistence\Doctrine\Entity\DoctrineUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class NotificationLogFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $id = EntityId::fromString('b2b2b2b2-a2b2-42b2-8b2b-b2b2b2b2b2b2');
        $entityId = EntityId::fromString('a1a1a1a1-a1a1-41a1-8a1a-a1a1a1a1a1a1');
        $messageId = EntityId::fromString('c1c1c1c1-c1c1-41c1-8c1c-c1c1c1c1c1c1');

        $notificationLog = new DoctrineNotificationLog()->fromDomain(
            new NotificationLog(
                id: $id,
                identityId: $entityId,
                userId: null,
                messageId: $messageId,
                type: NotificationType::WELCOME,
                sender: 'sms',
                status: NotificationStatus::SENT,
                statusMessage: 'Sent sms',
                createdAt: \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-01 14:45:00')
            )
        );

        $manager->persist($notificationLog);
        $manager->flush();
    }
}
