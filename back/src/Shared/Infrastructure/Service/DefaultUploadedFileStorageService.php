<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Exception\FileUploadException;
use App\Shared\Domain\Model\UploadedFile;
use App\Shared\Domain\Service\UploadedFileStorageService;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * @author Wilhelm Zwertvaegher
 */
class DefaultUploadedFileStorageService implements UploadedFileStorageService
{
    /**
     * @param iterable<UploadedFileStorageProvider> $providers
     */
    public function __construct(
        #[AutowireIterator('app.uploaded_file_storage_provider')]
        private iterable $providers
    ) {
    }

    private function findProvider(string $type): UploadedFileStorageProvider
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($type)) {
                return $provider;
            }
        }

        throw new FileUploadException('No provider found for type ' . $type);
    }

    public function upload(string $path, string $filename, string $type): UploadedFile
    {
        return $this->findProvider($type)->upload($path, $filename, $type);
    }

    public function delete(UploadedFile $uploadedFile): void
    {
        $this->findProvider($uploadedFile->getType())->delete($uploadedFile);
    }

    public function generateUrl(UploadedFile $uploadedFile): string
    {
        return $this->findProvider($uploadedFile->getType())->generateUrl($uploadedFile);
    }
}
