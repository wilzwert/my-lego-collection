<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\CollectionManagement\Domain\Model\BasePart;
use App\Shared\Domain\Model\EntityId;

final readonly class Part extends BasePart
{
    /**
     * @param EntityId $id
     * @param string $externalId
     * @param string $legoId
     * @param string $name
     * @param string $imagePath
     */
    public function __construct(
        private EntityId $id,
        string $externalId,
        string $legoId,
        string $name,
        string $imagePath
    ) {
        parent::__construct($externalId, $legoId, $name, $imagePath);
    }

    public static function create(string $externalId, string $legoId, string $name, string $imagePath): self
    {
        return new self(
            EntityId::generate(),
            $externalId,
            $legoId,
            $name,
            $imagePath
        );
    }

    public function getId(): EntityId
    {
        return $this->id;
    }
}
