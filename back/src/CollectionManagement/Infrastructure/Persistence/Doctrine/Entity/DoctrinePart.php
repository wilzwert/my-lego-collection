<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\Part;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Wilhelm Zwertvaegher
 */
#[ORM\Entity]
#[ORM\Table(name: "parts")]
class DoctrinePart
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(unique: true)]
    private string $externalId;

    #[ORM\Column]
    private string $legoId;

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private string $imagePath;

    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getLegoId(): string
    {
        return $this->legoId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function toDomain(): Part
    {
        return new Part(
            EntityId::fromString($this->id),
            $this->externalId,
            $this->legoId,
            $this->name,
            $this->imagePath
        );
    }

    public function fromDomain(Part $part): self
    {
        $this->id = $part->getId();
        $this->externalId = $part->getExternalId();
        $this->legoId = $part->getLegoId();
        $this->name = $part->getName();
        $this->imagePath = $part->getImagePath();
        return $this;
    }
}
