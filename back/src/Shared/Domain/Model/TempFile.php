<?php

namespace App\Shared\Domain\Model;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class TempFile
{
    public function __construct(private string $path, private string $originalFilename, private string $mime, private string $extension)
    {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    public function getMime(): string
    {
        return $this->mime;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }
}
