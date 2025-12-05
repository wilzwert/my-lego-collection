<?php

namespace MyLegoCollection\SharedEvent\Dto;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class UserDto
{
    public function __construct(
        private string $id,
        private string $identityId
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIdentityId(): string
    {
        return $this->identityId;
    }
}
