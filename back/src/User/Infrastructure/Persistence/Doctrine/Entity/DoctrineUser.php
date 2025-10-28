<?php

namespace App\User\Infrastructure\Persistence\Doctrine\Entity;

use App\Shared\Domain\Uuid;
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

    /**
     * @var list<string>
     */
    #[ORM\Column(type: "json")]
    private array $roles;

    #[ORM\Column(type: "string", nullable: false)]
    private string $passwordHash;

    /**
     * @param string $id
     * @param string $email
     * @param string $username
     * @param string $passwordHash
     * @param list<string> $roles
     */
    public function __construct(
        string $id,
        string $email,
        string $username,
        string $passwordHash,
        array $roles
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return list<string>
     */
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
            Uuid::fromString($this->id),
            $this->email,
            $this->username,
            $this->passwordHash,
            $this->roles
        );
    }
}
