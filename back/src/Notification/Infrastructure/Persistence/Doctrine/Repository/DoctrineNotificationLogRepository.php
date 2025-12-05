<?php

namespace App\Notification\Infrastructure\Persistence\Doctrine\Repository;

use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Ports\Driven\NotificationLogRepository;
use App\Notification\Infrastructure\Persistence\Doctrine\Entity\DoctrineNotificationLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @author Wilhelm Zwertvaegher
 * @extends ServiceEntityRepository<DoctrineNotificationLog>
 */
class DoctrineNotificationLogRepository extends ServiceEntityRepository implements NotificationLogRepository
{

    public function __construct(ManagerRegistry $managerRegistry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, DoctrineNotificationLog::class);
    }

    public function save(NotificationLog $notificationLog): void
    {
        // reloading to get an attached entity if it already exists
        $doctrineEntity = $this->find($notificationLog->getId()) ?? new DoctrineNotificationLog();
        $doctrineEntity->fromDomain($notificationLog);
        $this->entityManager->persist($doctrineEntity);
    }

    public function findByMessageId(string $messageId): array
    {
        return $this->findBy(['messageId' => $messageId]);
    }

    public function findByMessageIdAndSender(string $messageId, string $sender): array
    {
        return $this->findBy(['messageId' => $messageId, 'sender' => $sender]);
    }

    public function findByMessageIdAndSenderAndStatus(string $messageId, string $sender, NotificationStatus $status): array
    {
        return $this->findBy(['messageId' => $messageId, 'sender' => $sender, 'status' => $status]);
    }

    public function hasSuccess(string $messageId, string $sender): bool
    {
        return count($this->findByMessageIdAndSenderAndStatus($messageId, $sender, NotificationStatus::SENT)) > 0;
    }
}
