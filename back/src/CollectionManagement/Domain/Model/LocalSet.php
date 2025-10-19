<?php

namespace App\CollectionManagement\Domain\Model;

use App\Shared\Domain\Uuid;

final readonly class LocalSet extends Set
{
    /**
     * @param Uuid $id
     * @param string $externalId
     */
    public function __construct(
        private Uuid $id,
        string $legoId,
        string $externalId,
        string $name,
        string $partCount,
        string $imagePath,
        string $productionYear,
    )
    {
        parent::__construct($legoId, $externalId, $name, $partCount, $imagePath, $productionYear);
    }

    /**
     * @return Uuid
     */
     public function getId(): Uuid {
        return $this->id;
    }
}
