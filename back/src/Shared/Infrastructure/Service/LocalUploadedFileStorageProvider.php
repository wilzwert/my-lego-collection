<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Model\File;
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

    public function upload(string $path, string $filename, string $type): File
    {

        return new File(EntityId::generate(), 'uploaded_path', 'uploaded_filename', 'image/png', 'png', $type, new \DateTimeImmutable());
    }

    public function delete(File $uploadedFile): void
    {
        // TODO: Implement delete() method.
    }

    public function generateUrl(File $uploadedFile): string
    {
        // TODO: Implement generateUrl() method.
        return 'TODO';
    }

    public function supports(string $type): bool
    {
        return in_array($type, $this->supportedTypes);
    }
}
