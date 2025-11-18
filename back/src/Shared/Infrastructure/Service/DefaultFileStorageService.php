<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Exception\FileStorageException;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\TempFile;
use App\Shared\Domain\Service\FileStorageService;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class DefaultFileStorageService implements FileStorageService
{

    /**
     * @var array<FileStorageProvider>
     */
    private array $providers;

    /**
     * @param iterable<FileStorageProvider> $providers
     */
    public function __construct(
        #[AutowireIterator('app.file_storage_provider')]
        iterable $providers
    ) {
        $this->providers = iterator_to_array($providers);
    }

    private function findProvider(string $type): FileStorageProvider
    {
        return array_find($this->providers, fn (FileStorageProvider $provider) => $provider->supports($type)) ??
            throw new FileStorageException("No provider found for {$type}");
    }

    public function store(TempFile $tempFile, string $type): TempFile
    {
        return $this->findProvider($type)->store($tempFile, $type);
    }

    public function delete(StoredFile $storedFile): void
    {
        $this->findProvider($storedFile->getType())->delete($storedFile);
    }

    public function generateUrl(StoredFile $storedFile): string
    {
        return $this->findProvider($storedFile->getType())->generateUrl($storedFile);
    }
}
