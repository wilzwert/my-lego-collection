<?php

namespace App\User\Infrastructure\Persistence\Doctrine\Entity;

use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Wilhelm Zwertvaegher
 */

#[ORM\Entity]
#[ORM\Table(name: "users")]
class DoctrineUser
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 36, unique: true)]
    private string $identityId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;


    public function __construct(string $id, string $identityId, \DateTimeImmutable $createdAt, \DateTimeImmutable $updatedAt)
    {
        $this->id = $id;
        $this->identityId = $identityId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIdentityId(): string
    {
        return $this->identityId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function toDomain(): User
    {
        return new User(
            EntityId::fromString($this->id),
            EntityId::fromString($this->identityId),
            $this->createdAt,
            $this->updatedAt
        );
    }

    public static function fromDomain(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getIdentityId(),
            $user->getCreatedAt(),
            $user->getUpdatedAt()
        );
    }

}
