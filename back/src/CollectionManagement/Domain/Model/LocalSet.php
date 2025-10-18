<?php

namespace App\CollectionManagement\Domain\Model;

use App\Shared\Domain\Uuid;

final readonly class LocalSet implements Set
{
    /**
     * @param Uuid $id
     * @param string $externalId
     */
    public function __construct(private readonly Uuid $id, private readonly string $externalId)
    {}

    /**
     * @return Uuid
     */
     public function getId(): Uuid {
        return $this->id;
    }

    #[\Override]
    public function getExternalId(): string {
         return $this->externalId;
    }
}
