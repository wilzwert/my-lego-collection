<?php

namespace MyLegoCollection\SharedEvent\Dto;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class IdentityDto
{
    public function __construct(
        private string $id,
        private string $username,
        private string $email
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
