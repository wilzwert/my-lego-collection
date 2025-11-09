<?php

namespace App\User\Infrastructure\Dto;

use App\Auth\Application\Command\RegistrationCommand;
use App\Shared\Domain\Exception\ErrorCode;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateAvatarRequest
{
    private readonly string $contents;

    private readonly string $filename;

    public function __construct(
        string $contents,
        string $filename,
    ) {
        $this->contents = $contents;
        $this->filename = $filename;
    }

    public function getContents(): string
    {
        return $this->contents;
    }
    public function getFilename(): string
    {
        return $this->filename;
    }
}
