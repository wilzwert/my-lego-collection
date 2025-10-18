<?php

namespace App\CollectionManagement\Domain\Model;

use App\Shared\Domain\Uuid;

class UserSet
{
    public function __construct(private readonly Uuid $uuid, private readonly LocalSet $localSet)
    {}

    public function getLocalSet(): LocalSet
    {
        return $this->localSet;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

}
