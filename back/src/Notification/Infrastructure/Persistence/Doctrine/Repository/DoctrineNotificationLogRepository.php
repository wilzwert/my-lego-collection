<?php

namespace App\Notification\Infrastructure\Persistence\Doctrine\Repository;

use App\Notification\Domain\Model\NotificationLog;
use App\Notification\Domain\Model\NotificationStatus;
use App\Notification\Domain\Port\Driven\NotificationLogRepository;
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

    /**
     * @param string $messageId
     * @return array<NotificationLog>
     */
    public function findByMessageId(string $messageId): array
    {
        return array_map(fn(DoctrineNotificationLog $log) => $log->toDomain(), $this->findBy(['messageId' => $messageId]));
    }

    /**
     * @param string $messageId
     * @param string $sender
     * @return array<NotificationLog>
     */
    public function findByMessageIdAndSender(string $messageId, string $sender): array
    {
        return array_map(fn(DoctrineNotificationLog $log) => $log->toDomain(), $this->findBy(['messageId' => $messageId, 'sender' => $sender]));
    }

    /**
     * @param string $messageId
     * @param string $sender
     * @param NotificationStatus $status
     * @return array<NotificationLog>
     */
    public function findByMessageIdAndSenderAndStatus(string $messageId, string $sender, NotificationStatus $status): array
    {
        return array_map(fn(DoctrineNotificationLog $log) => $log->toDomain(), $this->findBy(['messageId' => $messageId, 'sender' => $sender, 'status' => $status]));
    }

    /**
     * @param string $messageId
     * @param NotificationStatus $status
     * @return array<NotificationLog>
     */
    public function findByMessageIdAndStatus(string $messageId, NotificationStatus $status): array
    {
        return array_map(fn(DoctrineNotificationLog $log) => $log->toDomain(), $this->findBy(['messageId' => $messageId, 'status' => $status]));
    }

    public function hasSuccess(string $messageId, string $sender): bool
    {
        return count($this->findByMessageIdAndSenderAndStatus($messageId, $sender, NotificationStatus::SENT)) > 0;
    }
}
