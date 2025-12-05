<?php

namespace MyLegoCollection\SharedContracts\Dto;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class IdentityDto
{
    public function __construct(
        private string $id,
        private string $email,
        private string $username
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

    public function getUsername(): string
    {
        return $this->username;
    }
}
