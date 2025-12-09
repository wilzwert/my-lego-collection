<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Repository;

use PHPUnit\Event\InvalidArgumentException;

/**
 * @author Wilhelm Zwertvaegher
 */
trait MapDoctrineEntityToDomainTrait
{
    /**
     * @template T
     * @template U
     * @param array<T> $doctrineEntities
     * @param class-string<T> $className
     * @return array<U>
     */
    private function mapEntitiesToDomain(array $doctrineEntities, string $className): array
    {
        return array_map(
        /**
         * @param T $doctrineEntity
         * @return U
         */
            fn ($doctrineEntity) => $doctrineEntity::class === $className ? $doctrineEntity->toDomain() : throw new InvalidArgumentException('Cannot map '.$doctrineEntity::class),
            $doctrineEntities
        );
    }

}
