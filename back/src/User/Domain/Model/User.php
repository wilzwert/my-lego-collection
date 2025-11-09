<?php

namespace App\User\Domain\Model;

use App\Shared\Domain\Model\File;
use App\Shared\Domain\Model\EntityId;

readonly class User
{
    /**
     * @param EntityId $id
     * @param EntityId $identityId
     */
    public function __construct(
        private EntityId           $id,
        private EntityId           $identityId,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
        private ?File $avatar = null
    ) {
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getIdentityId(): EntityId
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

    public function getAvatar(): ?File
    {
        return $this->avatar;
    }

    public function setAvatar(?File $avatar): self
    {
        return new User($this->id, $this->identityId, $this->createdAt, new \DateTimeImmutable(), $avatar);
    }
}

