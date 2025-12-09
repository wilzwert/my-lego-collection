<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Model\Local\Part;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Wilhelm Zwertvaegher
 */
#[ORM\Entity]
#[ORM\Table(name: "elements")]
class DoctrineElement
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 36)]
    private string $partId;

    #[ORM\Column(type: "string", length: 36)]
    private string $colorId;

    #[ORM\Column(unique: true)]
    private string $externalId;


    #[ORM\Column(type: "string")]
    private readonly string $name;

    #[ORM\Column(type: "string")]
    private string $imagePath;
    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPartId(): string
    {
        return $this->partId;
    }

    public function getColorId(): string
    {
        return $this->colorId;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImagePath(): string
    {
        return $this->imagePath;
    }

    public function toDomain(): Element
    {
        return new Element(
            EntityId::fromString($this->id),
            EntityId::fromString($this->partId),
            EntityId::fromString($this->colorId),
            $this->externalId,
            $this->name,
            $this->imagePath
        );
    }

    public function fromDomain(Element $element): self
    {
        $this->id = $element->getId();
        $this->partId = $element->getPartId();
        $this->colorId = $element->getColorId();
        $this->externalId = $element->getExternalId();
        $this->name = $element->getName();
        $this->imagePath = $element->getImagePath();
        return $this;
    }
}
