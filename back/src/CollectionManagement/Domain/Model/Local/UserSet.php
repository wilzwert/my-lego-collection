<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\Shared\Domain\Uuid;

class UserSet
{
    public function __construct(private readonly Uuid $uuid, private readonly Set $localSet)
    {}

    public function getLocalSet(): Set
    {
        return $this->localSet;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

}
