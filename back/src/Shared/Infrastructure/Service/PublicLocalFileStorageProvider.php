<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Exception\FileStorageException;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\TempFile;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
#[AutoconfigureTag('app.file_storage_provider')]
final readonly class PublicLocalFileStorageProvider implements FileStorageProvider
{
    private array $supportedTypes;

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly SluggerInterface $slugger,
        #[Autowire('%public_upload_dir%')] private string $uploadsDirectory,
        #[Autowire('%public_upload_base_url%')] private string $uploadsBaseUrl
    ) {
        $this->supportedTypes = ['user.avatar' => 'user-avatar'];
    }

    public function store(TempFile $tempFile, string $type): TempFile
    {
        $originalFilename = pathinfo($tempFile->getOriginalFilename(), PATHINFO_FILENAME);

        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$tempFile->getExtension();
        $newFilepath = $this->uploadsDirectory.'/'.$this->supportedTypes[$type].'/'.$newFilename;
        try {
            $this->filesystem->copy($tempFile->getPath(), $newFilepath);
            $this->filesystem->remove($tempFile->getPath());
        } catch (FileException $e) {
            throw new FileStorageException('Failed to copy temp file', 0, $e);
        }

        return new TempFile($newFilename, $tempFile->getOriginalFilename(), $tempFile->getMime(), $tempFile->getExtension());
    }

    public function delete(StoredFile $storedFile): void
    {
        try {
            $this->filesystem->remove($this->uploadsDirectory.'/'.$this->supportedTypes[$storedFile->getType()].'/'.$storedFile->getPath());
        } catch (FileException $e) {
            throw new FileStorageException('Failed to remove stored file', 0, $e);
        }
    }

    public function generateUrl(StoredFile $storedFile): string
    {
        return $this->uploadsBaseUrl.'/'.$this->supportedTypes[$storedFile->getType()].'/'.$storedFile->getPath();
    }

    public function supports(string $type): bool
    {
        return isset($this->supportedTypes[$type]);
    }
}
