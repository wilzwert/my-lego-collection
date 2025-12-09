<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\CollectionManagement\Domain\Model\Local\UserSetElement;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Wilhelm Zwertvaegher
 */
#[ORM\Entity]
#[ORM\Table(name: "user_set_elements")]
class DoctrineUserSetElement
{

    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 36)]
    private string $userSetId;

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

    public function getUserSetId(): string
    {
        return $this->userSetId;
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

    public function toDomain(): UserSetElement
    {
        return new UserSetElement(
            EntityId::fromString($this->id),
            EntityId::fromString($this->userSetId),
            EntityId::fromString($this->elementId),
            $this->count,
            $this->spareCount
        );
    }

    public function fromDomain(UserSetElement $userSetElement): self
    {
        $this->id = $userSetElement->getId();
        $this->userSetId = $userSetElement->getUserSetId();
        $this->elementId = $userSetElement->getElementId();
        $this->count = $userSetElement->getCount();
        $this->spareCount = $userSetElement->getSpareCount();
        return $this;
    }

}
