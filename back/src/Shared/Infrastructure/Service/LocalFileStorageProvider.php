<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\TempFile;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AutoconfigureTag('app.file_storage_provider')]
final readonly class LocalFileStorageProvider implements FileStorageProvider
{
    private array $supportedTypes;

    public function __construct(private readonly Filesystem $filesystem)
    {
        $this->supportedTypes = ['user.avatar'];
    }

    public function store(TempFile $tempFile, string $type): TempFile
    {
        // copy temp file to local storage
        return new TempFile('uploaded_path', 'uploaded_filename', 'image/png', 'png', $type, new \DateTimeImmutable());
    }

    public function delete(StoredFile $storedFile): void
    {
        // TODO: Implement delete() method.
    }

    public function generateUrl(StoredFile $storedFile): string
    {
        // TODO: Implement generateUrl() method.
        return 'TODO';
    }

    public function supports(string $type): bool
    {
        return in_array($type, $this->supportedTypes);
    }
}
