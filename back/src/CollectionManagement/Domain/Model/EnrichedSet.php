<?php

namespace App\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\Local\UserSet;

final readonly class EnrichedSet
{
    public function __construct(private readonly BaseSet $set, private readonly ?UserSet $userSet = null)
    {}

    public function getSet(): BaseSet
    {
        return $this->set;
    }

    public function getUserSet(): ?UserSet
    {
        return $this->userSet;
    }
}
