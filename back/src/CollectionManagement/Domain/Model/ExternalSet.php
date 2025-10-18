<?php

namespace App\CollectionManagement\Domain\Model;

use App\Shared\Domain\Uuid;

final readonly class ExternalSet implements Set
{
    /**
     * @param string $externalId
     */
    public function __construct(private readonly string $externalId)
    {}

    #[\Override]
    public function getExternalId(): string {
         return $this->externalId;
    }
}
