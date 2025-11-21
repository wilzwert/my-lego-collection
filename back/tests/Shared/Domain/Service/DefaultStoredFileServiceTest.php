<?php

namespace App\Tests\Shared\Domain\Service;

use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\TempFile;
use App\Shared\Domain\Repository\StoredFileRepository;
use App\Shared\Domain\Service\DefaultStoredFileService;
use App\Shared\Domain\Service\FileStorageService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class DefaultStoredFileServiceTest extends TestCase
{
    private EntityId $storedFileId;
    private StoredFileRepository $storedFileRepository;
    private FileStorageService $fileStorage;

    protected function setUp(): void
    {
        $this->storedFileId = EntityId::fromString('123e4567-e89b-42d3-a456-426614174000');

        $this->storedFileRepository = $this->createMock(StoredFileRepository::class);
        $this->fileStorage = $this->createMock(FileStorageService::class);

        $this->service = new DefaultStoredFileService(
            $this->storedFileRepository,
            $this->fileStorage
        );
    }


    #[Test]
    public function shouldReplaceStoredFile(): void
    {
        $this->storedFileRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(StoredFile::class));

        $this->fileStorage
            ->expects($this->never())
            ->method('delete');

        $this->storedFileRepository
            ->expects($this->never())
            ->method('delete');

        $now = new \DateTimeImmutable();
        $storedTempFile = new TempFile('stored_filepath', 'stored_filename', 'stored_mimetype', 'stored_extension');
        $tempFile = new TempFile('filepath', 'filename', 'mimetype', 'extension');

        $this->fileStorage
            ->expects($this->once())
            ->method('store')
            ->with($tempFile)
            ->willReturn($storedTempFile);

        $newStoredFile = $this->service->replace(null, $tempFile, 'file.type');

        self::assertNotNull($newStoredFile);
        self::assertEquals('stored_filepath', $newStoredFile->getPath());
        self::assertEquals('stored_filename', $newStoredFile->getFilename());
        self::assertEquals('stored_mimetype', $newStoredFile->getMimeType());
        self::assertEquals('stored_extension', $newStoredFile->getExtension());
        self::assertEquals('file.type', $newStoredFile->getType());
        self::assertGreaterThanOrEqual($now, $newStoredFile->getCreatedAt());
    }

    #[Test]
    public function shouldDeleteAndUpdateStoredFile(): void
    {
        $fileCreatedAt = new \DateTimeImmutable('2025-11-06T12:00:00');
        $oldFile = new StoredFile($this->storedFileId, 'old_stored_filepath', 'old_stored_filename', 'old_stored_mimetype', 'old_stored_extension', 'user.avatar', $fileCreatedAt);

        $this->fileStorage
            ->expects($this->once())
            ->method('delete')
            ->with($oldFile);

        $this->storedFileRepository
            ->expects($this->once())
            ->method('delete')
            ->with($oldFile);

        $now = new \DateTimeImmutable();
        $storedTempFile = new TempFile('stored_filepath', 'stored_filename', 'stored_mimetype', 'stored_extension');
        $tempFile = new TempFile('filepath', 'filename', 'mimetype', 'extension');

        $this->fileStorage
            ->expects($this->once())
            ->method('store')
            ->with($tempFile)
            ->willReturn($storedTempFile);

        $newStoredFile = $this->service->replace($oldFile, $tempFile, 'file.type');

        self::assertNotEquals($oldFile->getId(), $newStoredFile->getId());
        self::assertEquals('stored_filepath', $newStoredFile->getPath());
        self::assertEquals('stored_filename', $newStoredFile->getFilename());
        self::assertEquals('stored_mimetype', $newStoredFile->getMimeType());
        self::assertEquals('stored_extension', $newStoredFile->getExtension());
        self::assertEquals('file.type', $newStoredFile->getType());
        self::assertGreaterThanOrEqual($now, $newStoredFile->getCreatedAt());
    }

    #[Test]
    public function shouldDeleteStoredFile(): void
    {
        $fileCreatedAt = new \DateTimeImmutable('2025-11-06T12:00:00');
        $oldFile = new StoredFile($this->storedFileId, 'old_stored_filepath', 'old_stored_filename', 'old_stored_mimetype', 'old_stored_extension', 'user.avatar', $fileCreatedAt);

        $this->fileStorage
            ->expects($this->once())
            ->method('delete')
            ->with($oldFile);

        $this->storedFileRepository
            ->expects($this->once())
            ->method('delete')
            ->with($oldFile);

        $this->service->delete($oldFile);
    }
}
