<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\Color;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Wilhelm Zwertvaegher
 */

#[ORM\Entity]
#[ORM\Table(name: "colors")]
class DoctrineColor
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", unique: true)]
    private string $externalId;

    #[ORM\Column(type: "string", unique: true, nullable: true)]
    private string $legoId;

    #[ORM\Column(type: "string")]
    private string $name;

    #[ORM\Column(type: "string")]
    private string $rgbCode;



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

    public function getRgbCode(): string
    {
        return $this->rgbCode;
    }

    public function fromDomain(Color $color): self
    {
        $this->id = $color->getId();
        $this->externalId = $color->getExternalId();
        $this->legoId = $color->getLegoId();
        $this->name = $color->getName();
        $this->rgbCode = $color->getRgbCode();
        return $this;
    }

    public function toDomain(): Color
    {
        return new Color(EntityId::fromString($this->id), $this->externalId, $this->legoId, $this->name, $this->rgbCode);
    }
}
