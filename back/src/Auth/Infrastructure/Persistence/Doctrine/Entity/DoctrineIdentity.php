<?php

namespace App\Auth\Infrastructure\Persistence\Doctrine\Entity;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "identities")]
class DoctrineIdentity
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

    #[ORM\Column(type: "boolean", nullable: false, options: ["default" => false])]
    private bool $isComplete;
    #[ORM\Column(type: "string", length: 36, nullable: false, options: ["default" => ""])]
    private string $validationToken;

    /**
     */
    public function __construct(
    ) {
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

    public function fromDomain(Identity $identity): DoctrineIdentity
    {
        $this->id = $identity->getId();
        $this->email = $identity->getEmail();
        $this->username = $identity->getUsername();
        $this->roles = $identity->getRoles();
        $this->passwordHash = $identity->getPasswordHash();
        $this->isComplete = $identity->isComplete();
        $this->validationToken = $identity->getValidationToken();
        return $this;
    }

    public function toDomain(): Identity
    {
        return new Identity(
            id: EntityId::fromString($this->id),
            email: $this->email,
            username: $this->username,
            passwordHash: $this->passwordHash,
            roles: $this->roles,
            isComplete: $this->isComplete,
            validationToken: $this->validationToken
        );
    }
}
