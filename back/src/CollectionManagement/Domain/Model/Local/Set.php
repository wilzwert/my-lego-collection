<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\CollectionManagement\Domain\Model\BaseSet;
use App\Shared\Domain\Uuid;

/**
 * @author W. Zwertvaegher
 * A BaseSet that exists locally (i.e. saved in local DB)
 */

final readonly class Set extends BaseSet
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
