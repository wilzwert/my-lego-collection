<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\Shared\Domain\Model\EntityId;
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
    #[ORM\Column(unique: true)]
    private string $externalId;

    #[ORM\Column]
    private string $legoId;

    #[ORM\Column]
    private string $name;

    #[ORM\Column]
    private int $partCount;

    #[ORM\Column]
    private string $imagePath;

    #[ORM\Column]
    private int $productionYear;

    public function __construct(
        string $id,
        string $externalId,
        string $legoId,
        string $name,
        int $partCount,
        string $imagePath,
        int $productionYear
    ) {
        $this->id = $id;
        $this->externalId = $externalId;
        $this->legoId = $legoId;
        $this->name = $name;
        $this->partCount = $partCount;
        $this->imagePath = $imagePath;
        $this->productionYear = $productionYear;
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

    public function getPartCount(): int
    {
        return $this->partCount;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function getProductionYear(): int
    {
        return $this->productionYear;
    }

    public function toDomain(): Set
    {
        return new Set(
            EntityId::fromString($this->id),
            $this->externalId,
            $this->legoId,
            $this->name,
            $this->partCount,
            $this->imagePath,
            $this->productionYear
        );
    }
}
