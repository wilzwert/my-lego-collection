<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\Shared\Domain\Model\EntityId;

/**
 * A local user's set which consists of an id and a local set
 * TODO : add meaningful information, such as status (OWNED, WANTED...), status last update, rating...
 *
 * @author Wilhelm Zwertvaegher
 */
readonly class UserSet
{
    public function __construct(
        private EntityId $id,
        private EntityId $userId,
        private Set $localSet
    ) {
    }

    public function getLocalSet(): Set
    {
        return $this->localSet;
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getUserId(): EntityId
    {
        return $this->userId;
    }

}
