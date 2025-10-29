<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\CollectionManagement\Domain\Model\BaseSet;
use App\Shared\Domain\Uuid;

/**
 * @author Wilhelm Zwertvaegher
 * A BaseSet that exists locally (i.e. is saved locally)
 */

final readonly class Set extends BaseSet
{
    /**
     * @param Uuid $id
     * @param string $externalId
     * @param string $legoId
     * @param string $name
     * @param int $partCount
     * @param string $imagePath
     * @param int $productionYear
     */
    public function __construct(
        private Uuid $id,
        string $externalId,
        string $legoId,
        string $name,
        int $partCount,
        string $imagePath,
        int $productionYear,
    ) {
        parent::__construct($externalId, $legoId, $name, $partCount, $imagePath, $productionYear);
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }
}
