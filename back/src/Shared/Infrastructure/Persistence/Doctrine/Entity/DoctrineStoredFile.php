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

    #[ORM\Column(type: "string", unique: true)]
    private string $path;

    #[ORM\Column(type: "string")]
    private string $filename;

    #[ORM\Column(type: "string", length: 127)]
    private string $mimeType;

    #[ORM\Column(type: "string")]
    private string $extension;

    #[ORM\Column(type: "string", length: 40)]
    private string $type;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
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

    public function fromDomain(StoredFile $storedFile): self
    {
        if (isset($this->id) && !$storedFile->getId()->valueEquals($this->id)) {
            throw new \InvalidArgumentException('Mapping a StoredFile should not change its id');
        }

        $this->id = $storedFile->getId();
        $this->path = $storedFile->getPath();
        $this->filename = $storedFile->getFilename();
        $this->mimeType = $storedFile->getMimeType();
        $this->extension = $storedFile->getExtension();
        $this->type = $storedFile->getType();
        $this->createdAt = $storedFile->getCreatedAt();

        return $this;
    }
}
