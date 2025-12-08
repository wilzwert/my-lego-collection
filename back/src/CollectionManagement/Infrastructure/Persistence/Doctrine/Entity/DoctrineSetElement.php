<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Wilhelm Zwertvaegher
 */
#[ORM\Entity]
#[ORM\Table(name: "set_elements")]
class DoctrineSetElement
{

    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 36)]
    private string $setId;

    #[ORM\Column(type: "string", length: 36)]
    private string $elementId;

    #[ORM\Column(type: "integer")]
    private int $count;

    #[ORM\Column(type: "integer")]
    private int $spareCount;

    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSetId(): string
    {
        return $this->setId;
    }

    public function getElementId(): string
    {
        return $this->elementId;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getSpareCount(): int
    {
        return $this->spareCount;
    }

    public function toDomain(): SetElement
    {
        return new SetElement(
            EntityId::fromString($this->id),
            EntityId::fromString($this->setId),
            EntityId::fromString($this->elementId),
            $this->count,
            $this->spareCount
        );
    }

    public function fromDomain(SetElement $setElement): self
    {
        $this->id = $setElement->getId();
        $this->setId = $setElement->getSetId();
        $this->elementId = $setElement->getElementId();
        $this->count = $setElement->getCount();
        $this->spareCount = $setElement->getSpareCount();
        return $this;
    }

}
