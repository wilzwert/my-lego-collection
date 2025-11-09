<?php

namespace App\Shared\Domain\Model;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class File
{
    public function __construct(
        private EntityId           $id,
        private string             $path,
        private string             $filename,
        private string             $mimeType,
        private string             $extension,
        private string             $type,
        private \DateTimeImmutable $createdAt
    ) {
    }

    public function getId(): EntityId
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCreatedAt(): \DateTimeImmutable

    {
        return $this->createdAt;
    }
}
