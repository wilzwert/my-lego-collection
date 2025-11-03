<?php

namespace App\User\Infrastructure\Dto;

use App\User\Domain\Model\User;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: User::class)]
final class UserDto
{
    public string $id;

    public \DateTimeImmutable $createdAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
