<?php

namespace App\User\Infrastructure\Dto;

use App\Shared\Domain\Model\StoredFile;
use App\Shared\Infrastructure\Dto\StoredFileDto;
use App\Shared\Infrastructure\Service\StoredFileDtoTransformer;
use App\User\Domain\Model\User;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: User::class)]
final class UserDto
{
    public function __construct(
        public string $id,
        public \DateTimeImmutable $createdAt,
        // #[Map(if: false)
        #[Map(source: 'avatar', transform: StoredFileDtoTransformer::class)]
        public ?StoredFileDto $avatar = null
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getAvatar(): ?StoredFileDto
    {
        return $this->avatar;
    }
}
