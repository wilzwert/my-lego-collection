<?php

namespace App\User\Infrastructure\Persistence\Doctrine\Entity;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Infrastructure\Persistence\Doctrine\Entity\DoctrineStoredFile;
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

    #[ORM\OneToOne(targetEntity: DoctrineStoredFile::class)]
    #[ORM\JoinColumn(name: "avatar_id", referencedColumnName: "id", nullable: true)]
    private ?DoctrineStoredFile $avatar;

    public function __construct(/*string $id, string $identityId, \DateTimeImmutable $createdAt, \DateTimeImmutable $updatedAt, ?DoctrineStoredFile $avatar*/)
    {
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

    public function getAvatar(): ?DoctrineStoredFile
    {
        return $this->avatar;
    }

    public function toDomain(): User
    {
        return new User(
            EntityId::fromString($this->id),
            EntityId::fromString($this->identityId),
            $this->createdAt,
            $this->updatedAt,
            $this->avatar?->toDomain()
        );
    }

    public function fromDomain(User $user, ?DoctrineStoredFile $doctrineStoredFile = null): self
    {
        if (isset($this->identityId) && !$user->getIdentityId()->valueEquals($this->identityId) ||
            isset($this->id) && !$user->getId()->valueEquals($this->id)) {
            throw new \InvalidArgumentException('Mapping a user should not change its id or identityid');
        }

        if ($user->getAvatar() !== null && null === $doctrineStoredFile) {
            throw new \InvalidArgumentException('User seems to have an avatar but not doctrine stored file');
        }

        $this->id = $user->getId();
        $this->identityId = $user->getIdentityId();
        $this->createdAt = $user->getCreatedAt();
        $this->updatedAt = $user->getUpdatedAt();
        $this->avatar = $doctrineStoredFile;
        return $this;
    }
}
