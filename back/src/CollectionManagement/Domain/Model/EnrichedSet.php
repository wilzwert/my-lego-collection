<?php

namespace App\CollectionManagement\Domain\Model;

final readonly class EnrichedSet
{
    public function __construct(private readonly Set $set, private readonly ?UserSet $userSet = null)
    {}

    public function getSet(): Set
    {
        return $this->set;
    }

    public function getUserSet(): ?UserSet
    {
        return $this->userSet;
    }
}
