<?php

namespace App\Notification\Infrastructure\Persistence\Doctrine\Repository;

use App\Notification\Domain\Model\NotificationLog;
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
}
