<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\Shared\Domain\Uuid;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sets")
 */
class DoctrineSet
{
    #[ORM\Id, ORM\Column(type: "string")]
    private string $id;

    // TODO : uniqueness
    #[ORM\Column(type: "string", unique: true)]
    private string $externalId;



    public function __construct(string $id, string $externalId)
    {
        $this->id = $id;
        $this->externalId = $externalId;
    }

    public function toDomain(): Set
    {
        return new Set(Uuid::fromString($this->id), $this->externalId);
    }
}

