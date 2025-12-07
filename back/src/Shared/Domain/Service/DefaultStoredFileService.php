<?php

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\TempFile;
use App\Shared\Domain\Port\Driven\FileStorageService;
use App\Shared\Domain\Port\Driven\StoredFileRepository;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class DefaultStoredFileService implements StoredFileService
{

    public function __construct(
        private StoredFileRepository $storedFileRepository,
        private FileStorageService  $fileStorage,
    ) {
    }

    public function replace(?StoredFile $storedFile, TempFile $tempFile, string $fileType): StoredFile
    {
        if ($storedFile) {
            $this->fileStorage->delete($storedFile);
            $this->storedFileRepository->delete($storedFile);
        }

        $storedTempFile = $this->fileStorage->store($tempFile, $fileType);
        $storedFile = new StoredFile(
            EntityId::generate(),
            $storedTempFile->getPath(),
            $storedTempFile->getOriginalFilename(),
            $storedTempFile->getMime(),
            $storedTempFile->getExtension(),
            $fileType,
            new \DateTimeImmutable()
        );

        $this->storedFileRepository->save($storedFile);
        return $storedFile;
    }

    public function delete(StoredFile $storedFile): void
    {
        $this->fileStorage->delete($storedFile);
        $this->storedFileRepository->delete($storedFile);
    }
}
