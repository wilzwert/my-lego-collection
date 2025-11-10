<?php

namespace App\Shared\Infrastructure\Persistence\Doctrine\Entity;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Wilhelm Zwertvaegher
 */

#[ORM\Entity]
#[ORM\Table(name: "stored_files")]
class DoctrineStoredFile
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Id, ORM\Column(type: "string")]
    private string $path;

    #[ORM\Id, ORM\Column(type: "string")]
    private string $filename;

    #[ORM\Id, ORM\Column(type: "string", length: 127)]
    private string $mimeType;

    #[ORM\Id, ORM\Column(type: "string")]
    private string $extension;

    #[ORM\Id, ORM\Column(type: "string", length: 40)]
    private string $type;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * @param string $id
     * @param string $path
     * @param string $filename
     * @param string $mimeType
     * @param string $extension
     * @param string $type
     * @param \DateTimeImmutable $createdAt
     */
    public function __construct(string $id, string $path, string $filename, string $mimeType, string $extension, string $type, \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->path = $path;
        $this->filename = $filename;
        $this->mimeType = $mimeType;
        $this->extension = $extension;
        $this->type = $type;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
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

    public function toDomain(): StoredFile
    {
        return new StoredFile(
            EntityId::fromString($this->id),
            $this->path,
            $this->filename,
            $this->mimeType,
            $this->extension,
            $this->type,
            $this->createdAt
        );
    }
}
