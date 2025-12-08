<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\Notification\Domain\Model\NotificationType;
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

    #[ORM\Column(type: "string", enumType: SetCreationStatus::class)]
    private SetCreationStatus $creationStatus;

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
            $this->productionYear,
            $this->creationStatus
        );
    }

    public function fromDomain(Set $set): self
    {
        $this->id = $set->getId();
        $this->externalId = $set->getExternalId();
        $this->legoId = $set->getLegoId();
        $this->name = $set->getName();
        $this->partCount = $set->getPartCount();
        $this->imagePath = $set->getImagePath();
        $this->productionYear = $set->getProductionYear();
        $this->creationStatus = $set->getCreationStatus();
        return $this;
    }
}
