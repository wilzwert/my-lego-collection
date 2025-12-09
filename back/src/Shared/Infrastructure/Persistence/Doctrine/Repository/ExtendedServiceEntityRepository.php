<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Event\InvalidArgumentException;

/**
 * @template T the class of the doctrine entity
 * @template U the class of the domain entity
 * @extends ServiceEntityRepository<T>
 * @author Wilhelm Zwertvaegher
 */
abstract class ExtendedServiceEntityRepository extends ServiceEntityRepository
{
    use MapDoctrineEntityToDomainTrait;

    public function __construct(ManagerRegistry $managerRegistry, string $entityClass, protected readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($managerRegistry, $entityClass);
    }


    /**
     * @param array<T> $elements
     * @return array<U>
     */
    protected function mapToDomain(array $elements): array
    {
        /**
         * @var class-string<T> $className
         */
        $className = parent::getClassName();
        return $this->mapEntitiesToDomain($elements, $className);
    }

    /**
     * @param T $domainEntity
     * @return void
     */
    protected function attachAndSave($domainEntity): void
    {
        // reloading to get an attached entity if it already exists
        $className = parent::getClassName();
        $doctrineEntity = $this->find($domainEntity->getId()) ?? new $className();
        $doctrineEntity->fromDomain($domainEntity);
        $this->entityManager->persist($doctrineEntity);
    }

    /**
     * @param array<T> $elements
     * @return void
     * @throws ORMException
     */
    protected function attachAndSaveAll(array $elements): void
    {

        $ids = array_map(fn ($part) => $part->getId(), $elements);

        $qb = $this->createQueryBuilder('e');
        $existingIds = $qb->select('e.id')
            ->where($qb->expr()->in('e.id', ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getScalarResult();
        $existingIds = array_map(fn ($r) => $r['id'], $existingIds);
        $existingMap = array_fill_keys($existingIds, true);

        $class = parent::getClassName();

        foreach ($elements as $element) {
            $id = $element->getId()->__toString();
            if (isset($existingMap[$id])) {
                $managed = $this->entityManager->getReference($class, $id);
                $managed->fromDomain($element);
            } else {
                $entity = new $class();
                $entity->fromDomain($element);
                $this->entityManager->persist($entity);
            }
        }
    }
}
