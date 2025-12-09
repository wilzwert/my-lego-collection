<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\CollectionManagement\Domain\Model\Local\UserElement;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Wilhelm Zwertvaegher
 */
#[ORM\Entity]
#[ORM\Table(name: "user_elements")]
class DoctrineUserElement
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 36, index: true)]
    private string $userId;

    #[ORM\Column(type: "string", length: 36)]
    private string $elementId;

    #[ORM\Column(type: "integer")]
    private int $setCount;

    #[ORM\Column(type: "integer")]
    private int $spareCount;

    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getElementId(): string
    {
        return $this->elementId;
    }

    public function getSetCount(): int
    {
        return $this->setCount;
    }

    public function getSpareCount(): int
    {
        return $this->spareCount;
    }

    public function toDomain(): UserElement
    {
        return new UserElement(
            EntityId::fromString($this->id),
            EntityId::fromString($this->userId),
            EntityId::fromString($this->elementId),
            $this->setCount,
            $this->spareCount
        );
    }

    public function fromDomain(UserElement $userElement): self
    {
        $this->id = $userElement->getId();
        $this->userId = $userElement->getUserId();
        $this->elementId = $userElement->getElementId();
        $this->setCount = $userElement->getSetCount();
        $this->spareCount = $userElement->getSpareCount();
        return $this;
    }

}
