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

    public function upload(string $path, string $filename, string $type): UploadedFile
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($type)) {
                return $provider->upload($path, $filename, $type);
            }
        }

        throw new FileUploadException('No provider found for type ' . $type);
    }

    public function delete(UploadedFile $uploadedFile): void
    {
        foreach ($this->providers as $provider) {
            if($provider->supports($uploadedFile->getType())) {
                $provider->delete($uploadedFile);
                return;
            }
        }

        throw new FileUploadException('No provider found for type ' . $uploadedFile->getType());
    }

    public function generateUrl(UploadedFile $uploadedFile): string
    {
        foreach ($this->providers as $provider) {
            if($provider->supports($uploadedFile->getType())) {
                return $provider->generateUrl($uploadedFile);
            }
        }

        throw new FileUploadException('Unable to generate url for type' . $uploadedFile->getType());
    }
}
