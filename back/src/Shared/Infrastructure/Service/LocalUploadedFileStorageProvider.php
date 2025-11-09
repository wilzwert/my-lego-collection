<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Model\UploadedFile;
use App\Shared\Domain\Model\Uuid;
use App\Shared\Domain\Service\UploadedFileStorageService;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AutoconfigureTag('app.uploaded_file_storage_provider')]
final readonly class LocalUploadedFileStorageProvider implements UploadedFileStorageProvider
{

    public function upload(string $path, string $filename, string $type): UploadedFile
    {
        return new UploadedFile(Uuid::generate(), 'uploaded_path', 'uploaded_filename', 'image/png', 'png', $type, new \DateTimeImmutable());
    }

    public function delete(UploadedFile $uploadedFile): void
    {
        // TODO: Implement delete() method.
    }

    public function generateUrl(UploadedFile $uploadedFile): string
    {
        // TODO: Implement generateUrl() method.
        return 'TODO';
    }

    public function supports(string $type): bool
    {
        // as of now, local storage for all
        return true;
    }
}
