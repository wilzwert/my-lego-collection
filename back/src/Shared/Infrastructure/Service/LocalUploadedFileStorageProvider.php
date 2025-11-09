<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Model\UploadedFile;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\UploadedFileStorageService;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AutoconfigureTag('app.uploaded_file_storage_provider')]
final readonly class LocalUploadedFileStorageProvider implements UploadedFileStorageProvider
{
    private array $supportedTypes;

    public function __construct()
    {
        $this->supportedTypes = ['user.avatar'];
    }

    public function upload(string $path, string $filename, string $type): UploadedFile
    {

        return new UploadedFile(EntityId::generate(), 'uploaded_path', 'uploaded_filename', 'image/png', 'png', $type, new \DateTimeImmutable());
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
        return in_array($type, $this->supportedTypes);
    }
}
