<?php

namespace App\User\Infrastructure\Persistence\Doctrine\Entity;

use App\User\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class DoctrineUser
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: "string", length: 60, unique: true)]
    private string $username;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: "string", nullable: false)]
    private string $passwordHash;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->email = $user->getEmail();
        $this->username = $user->getUsername();
        $this->roles = $user->getRoles();
        $this->passwordHash = $user->getPasswordHash();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(?string $hash): void
    {
        $this->passwordHash = $hash;
    }

    public function toDomain(): User
    {
        return new User(
            $this->id,
            $this->email,
            $this->username,
            $this->passwordHash,
            $this->roles
        );
    }
}

