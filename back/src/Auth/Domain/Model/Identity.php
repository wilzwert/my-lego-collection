<?php

namespace App\Auth\Domain\Model;

use App\Auth\Domain\Exception\AuthErrorCode;
use App\Shared\Domain\Exception\ErrorCode;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Validation\Validator;

readonly class Identity
{
    /**
     * @param EntityId $id
     * @param string $email
     * @param string $username
     * @param string $passwordHash
     * @param list<string> $roles
     * @throws ValidationException
     */
    public function __construct(
        private EntityId $id,
        public string    $email,
        private string   $username,
        private string   $passwordHash,
        private array    $roles = ['ROLE_USER']
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
}
