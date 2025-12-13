<?php

namespace App\CollectionManagement\Domain\Port\Driven;

use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\CollectionManagement\Domain\Model\Local\UserElement;
use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;

/**
 * @author Wilhelm Zwertvaegher
 */
interface UserElementRepository
{
    public function save(UserElement $setElement): void;

    /**
     * @param array<UserElement> $userElements
     * @return void
     */
    public function saveAll(array $userElements): void;

    /**
     * @param EntityId $userId
     * @return array<UserElement>
     */
    public function findByUserId(EntityId $userId): array;

    /**
     * @param array<EntityId> $elementsIds
     * @return array<UserElement>
     */
    public function findByElementsIds(array $elementsIds): array;

    /**
     * @param EntityId $userId
     * @param array<EntityId> $elementsIds
     * @return array<UserElement>
     */
    public function findByUserIdAndElementsIds(EntityId $userId, array $elementsIds): array;

}
