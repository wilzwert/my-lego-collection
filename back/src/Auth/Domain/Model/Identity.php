<?php

namespace App\Auth\Domain\Model;

use App\Auth\Domain\Event\EmailChangedEvent;
use App\Auth\Domain\Event\IdentityCompletedEvent;
use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Exception\AuthErrorCode;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\ProducesDomainEvents;
use App\Shared\Domain\Validation\Validator;

final class Identity implements ProducesDomainEvents
{

    /**
     * @var array<DomainEvent>
     */
    private array $events = [];

    /**
     * @param EntityId $id
     * @param string $email
     * @param string $username
     * @param string $passwordHash
     * @param list<string> $roles
     * @param boolean $isComplete
     * @param string $validationToken
     * @throws ValidationException
    */
    public function __construct(
        private readonly EntityId $id,
        public readonly string    $email,
        private readonly string   $username,
        private readonly string   $passwordHash,
        private readonly array    $roles = ['ROLE_USER'],
        private readonly bool $isComplete = false,
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
    }

    public static function create(
        EntityId $id,
        string    $email,
        string   $username,
        string   $passwordHash,
        array    $roles = ['ROLE_USER']
    ): self {
        $newUser = new self(
            id: $id,
            email: $email,
            username: $username,
            passwordHash: $passwordHash,
            roles: $roles,
            isComplete: false,
        );
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

    public function isComplete(): bool
    {
        return $this->isComplete;
    }

    public function getValidationToken(): string
    {
        return $this->validationToken;
    }

    /**
     * @return array<DomainEvent>
     */
    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    public function complete(): self
    {
        if ($this->isComplete()) {
            return $this;
        }

        $new = new self(
            id: $this->id,
            email: $this->email,
            username: $this->username,
            passwordHash: $this->passwordHash,
            roles: $this->roles,
            isComplete: true,
            validationToken: $this->generateValidationToken()
        );
        $new->events = [new IdentityCompletedEvent($new)];
        return $new;
    }

    public function changeEmail(string $email): self
    {
        $new = new self(
            id: $this->id,
            email: $email,
            username: $this->username,
            passwordHash: $this->passwordHash,
            roles: $this->roles,
            isComplete: true,
            validationToken: $this->generateValidationToken()
        );
        $new->events = [new EmailChangedEvent($new)];
        return $new;
    }

    private function generateValidationToken(): string
    {
        return EntityId::generate();
    }
}
