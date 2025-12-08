<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetStatus;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_sets")
 */
class DoctrineUserSet
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 36)]
    private string $userId;

    #[ORM\ManyToOne(targetEntity: DoctrineSet::class)]
    #[ORM\JoinColumn(name: "set_id", referencedColumnName: "id", nullable: true)]
    private DoctrineSet $set;

    #[ORM\Column(type: "string", enumType: UserSetCreationStatus::class)]
    private UserSetCreationStatus $creationStatus;

    #[ORM\Column(type: "string", nullable: true, enumType: UserSetStatus::class)]
    private ?UserSetStatus $status;


    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function fromDomain(UserSet $userSet): self
    {
        $this->id = $userSet->getId();
        $this->userId = $userSet->getUserId();
        $this->set = new DoctrineSet()->fromDomain($userSet->getSet());
        $this->creationStatus = $userSet->getCreationStatus();
        $this->status = $userSet->getStatus();
        return $this;
    }

    public function toDomain(): UserSet
    {
        return new UserSet(
            EntityId::fromString($this->id),
            EntityId::fromString($this->userId),
            $this->set->toDomain(),
            $this->creationStatus,
            $this->status
        );
    }
}
