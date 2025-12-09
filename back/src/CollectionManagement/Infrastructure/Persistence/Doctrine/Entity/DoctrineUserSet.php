<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetStatus;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

#[ORM\Entity]
#[ORM\Table(name: "user_sets")]
#[ORM\UniqueConstraint(name: "uniq_user_set", columns: ["user_id", "set_id"])]
class DoctrineUserSet
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 36, index: true)]
    private string $userId;

    #[ORM\Column(type: "string", length: 36, index: true)]
    private string $setId;

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

    public function fromDomain(UserSet $userSet): self
    {
        $this->id = $userSet->getId();
        $this->userId = $userSet->getUserId();
        $this->setId = $userSet->getSetId();
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
            EntityId::fromString($this->setId),
            $this->createdAt,
            $this->creationStatus,
            $this->status,
            $this->statusDate
        );
    }
}
