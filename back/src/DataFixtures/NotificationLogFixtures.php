<?php

namespace App\DataFixtures;

use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Model\NotificationType;
use App\Notification\Infrastructure\Persistence\Doctrine\Entity\DoctrineNotificationLog;
use App\Shared\Domain\Model\EntityId;
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

        // successful sms notification log
        $notificationLog = new DoctrineNotificationLog()->fromDomain(
            new NotificationLog(
                id: $id,
                identityId: $entityId,
                userId: null,
                messageId: EntityId::fromString('c1c1c1c1-c1c1-41c1-8c1c-c1c1c1c1c1c1'),
                type: NotificationType::WELCOME,
                sender: 'sms',
                status: NotificationStatus::SENT,
                statusMessage: 'Sent sms',
                createdAt: \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-01 14:45:00')
            )
        );

        // failed sms notification log
        $notificationLog2 = new DoctrineNotificationLog()->fromDomain(
            new NotificationLog(
                id: EntityId::generate(),
                identityId: $entityId,
                userId: null,
                messageId: EntityId::fromString('f03ba44d-4cd2-47f8-845e-a42fd98fc137'),
                type: NotificationType::WELCOME,
                sender: 'sms',
                status: NotificationStatus::ERROR,
                statusMessage: 'Error sending sms',
                createdAt: \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-01 12:45:00')
            )
        );

        // failed email notification log for user2
        $notificationLog3 = new DoctrineNotificationLog()->fromDomain(
            new NotificationLog(
                id: EntityId::generate(),
                identityId: EntityId::fromString('0efa63b0-3291-4da3-9dcc-0c7ea1d538d0'),
                userId: null,
                messageId: EntityId::fromString('267242f2-2780-4ef2-b0b8-93d6099e396b'),
                type: NotificationType::WELCOME,
                sender: 'email',
                status: NotificationStatus::ERROR,
                statusMessage: 'Error sending email',
                createdAt: \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2025-11-02 12:46:00')
            )
        );

        $manager->persist($notificationLog);
        $manager->persist($notificationLog2);
        $manager->persist($notificationLog3);
        $manager->flush();
    }
}
