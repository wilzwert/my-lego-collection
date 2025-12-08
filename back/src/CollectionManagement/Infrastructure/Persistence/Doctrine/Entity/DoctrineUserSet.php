<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetStatus;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

#[ORM\Entity]
#[ORM\Table(
    name: "user_sets",
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: "uniq_user_set", columns: ["user_id", "set_id"])
    ]
)]
class DoctrineUserSet
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 36)]
    private string $userId;

    #[ORM\ManyToOne(targetEntity: DoctrineSet::class)]
    #[ORM\JoinColumn(name: "set_id", referencedColumnName: "id", nullable: true)]
    private DoctrineSet $set;

    #[ORM\Column(type: "datetime_immutable", index: true)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: "string", enumType: UserSetCreationStatus::class)]
    private UserSetCreationStatus $creationStatus;

    #[ORM\Column(type: "string", nullable: true, enumType: UserSetStatus::class)]
    private ?UserSetStatus $status;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $statusDate;

    public function __construct()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function fromDomain(UserSet $userSet, DoctrineSet $doctrineSet): self
    {
        $this->id = $userSet->getId();
        $this->userId = $userSet->getUserId();
        $this->set = $doctrineSet;
        $this->createdAt = $userSet->getCreatedAt();
        $this->creationStatus = $userSet->getCreationStatus();
        $this->status = $userSet->getStatus();
        $this->statusDate = $userSet->getStatusDate();
        return $this;
    }

    public function toDomain(): UserSet
    {
        return new UserSet(
            EntityId::fromString($this->id),
            EntityId::fromString($this->userId),
            $this->set->toDomain(),
            $this->createdAt,
            $this->creationStatus,
            $this->status,
            $this->statusDate
        );
    }
}
