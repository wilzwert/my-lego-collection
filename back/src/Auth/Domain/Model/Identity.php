<?php

namespace App\Auth\Domain\Model;

use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Exception\AuthErrorCode;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Exception\ErrorCode;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Validation\Validator;

class Identity
{
    /**
     * @var array<DomainEvent>
     */
    private array $events;



    /**
     * @param EntityId $id
     * @param string $email
     * @param string $username
     * @param string $passwordHash
     * @param list<string> $roles
     * @param string $validationToken
    * @throws ValidationException
     */
    public function __construct(
        private readonly EntityId $id,
        public readonly string    $email,
        private readonly string   $username,
        private readonly string   $passwordHash,
        private readonly array    $roles = ['ROLE_USER'],
        private readonly string $validationToken = ''
    ) {
        $validator = new Validator();
        $validator
            ->requireNotEmpty('id', $this->id)
            ->requireNotEmpty('email', $this->email)
            ->requireValidEmail('email', $this->email)
            ->require('username', fn () => (bool)preg_match('/^[a-zA-Z0-9_-]+$/', $this->username), AuthErrorCode::INVALID_USERNAME)
            ->requireNotEmpty('passwordHash', $this->passwordHash)
            ->requireNotEmpty('roles', $this->roles)
            ->validate();
        $this->events = [];
    }

    public static function create(
        EntityId $id,
        string    $email,
        string   $username,
        string   $passwordHash,
        array    $roles = ['ROLE_USER']
    ): self
    {
        $newUser = new self($id, $email, $username, $passwordHash, $roles);
        $newUser->events = [new IdentityCreatedEvent($newUser)];
        return $newUser;
    }

    public function getId(): EntityId
    {
        return $this->id;
    }


    public function getEmail(): string
    {
        return $this->email;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getValidationToken(): string
    {
        return $this->validationToken;
    }

    /**
     * @return array<DomainEvent>
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function generateValidationToken(): self
    {
        $result = new self(
            $this->id,
            $this->email,
            $this->username,
            $this->passwordHash,
            $this->roles,
            EntityId::generate()
        );
        $result->events = [];
        return $result;
    }
}
