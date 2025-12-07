<?php

namespace App\Tests\Shared\Unit\Infrastructure\Service;

use App\Shared\Domain\Exception\FileStorageException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\TempFile;
use App\Shared\Infrastructure\Service\PublicLocalFileStorageProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

/**
 * @author Wilhelm Zwertvaegher
 */


final class PublicLocalFileStorageProviderTest extends TestCase
{
    private Filesystem&MockObject $filesystem;
    private SluggerInterface&MockObject $slugger;
    private PublicLocalFileStorageProvider $provider;
    private string $uploadsDir;
    private string $uploadsBaseUrl;

    private StoredFile $testStoredFile;

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->slugger = $this->createMock(SluggerInterface::class);
        $this->uploadsDir = '/tmp/uploads';
        $this->uploadsBaseUrl = 'http://localhost/uploads';
        $this->provider = new PublicLocalFileStorageProvider(
            $this->filesystem,
            $this->slugger,
            $this->uploadsDir,
            $this->uploadsBaseUrl
        );

        $entityId = EntityId::generate();
        $createdAt = new \DateTimeImmutable('2025-11-01 10:00:00');
        $this->testStoredFile = new StoredFile($entityId, 'avatar.png', 'avatar.png', 'image/png', 'png', 'user.avatar', $createdAt);
    }

    #[Test]
    public function shouldReturnTrueIfTypeIsSupported(): void
    {
        $this->slugger->expects($this->never())->method('slug');
        $this->filesystem->expects($this->never())->method('copy');
        $this->filesystem->expects($this->never())->method('remove');
        self::assertTrue($this->provider->supports('user.avatar'));
    }

    #[Test]
    public function shouldReturnFalseIfTypeIsNotSupported(): void
    {
        $this->slugger->expects($this->never())->method('slug');
        $this->filesystem->expects($this->never())->method('copy');
        $this->filesystem->expects($this->never())->method('remove');
        self::assertFalse($this->provider->supports('unknown.type'));
    }

    #[Test]
    public function shouldStoreTempFileAndReturnNewTempFile(): void
    {
        $tempFile = new TempFile('temp.png', 'avatar.png', 'image/png', 'png');

        $this->slugger
            ->expects($this->once())
            ->method('slug')
            ->with('avatar')
            ->willReturn(new UnicodeString('avatar'));

        $this->filesystem
            ->expects($this->once())
            ->method('copy')
            ->with(
                $tempFile->getPath(),
                self::callback(
                    fn (string $path) =>
                    str_starts_with($path, $this->uploadsDir . '/user-avatar/avatar-')
                    && str_ends_with($path, '.png')
                )
            );

        $this->filesystem
            ->expects($this->once())
            ->method('remove')
            ->with($tempFile->getPath());

        $result = $this->provider->store($tempFile, 'user.avatar');

        self::assertNotSame($tempFile->getPath(), $result->getPath());
        self::assertStringEndsWith('.png', $result->getPath());
    }

    #[Test]
    public function shouldThrowFileStorageExceptionWhenCopyFails(): void
    {
        $tempFile = new TempFile('temp.png', 'avatar.png', 'image/png', 'png');

        $this->slugger
            ->expects($this->once())
            ->method('slug')
            ->willReturn(new UnicodeString('avatar'));

        $this->filesystem
            ->expects($this->once())
            ->method('copy')
            ->willThrowException(new FileException('error'));

        $this->expectException(FileStorageException::class);
        $this->expectExceptionMessage('Failed to copy temp file');

        $this->provider->store($tempFile, 'user.avatar');
    }

    #[Test]
    public function shouldDeleteStoredFile(): void
    {
        $this->slugger->expects($this->never())->method('slug');
        $this->filesystem
            ->expects($this->once())
            ->method('remove')
            ->with($this->uploadsDir . '/user-avatar/avatar.png');

        $this->provider->delete($this->testStoredFile);
    }

    #[Test]
    public function shouldThrowFileStorageExceptionWhenDeleteFails(): void
    {
        $this->slugger->expects($this->never())->method('slug');
        $this->filesystem
            ->expects($this->once())
            ->method('remove')
            ->willThrowException(new FileException('remove failed'));

        $this->expectException(FileStorageException::class);
        $this->expectExceptionMessage('Failed to remove stored file');

        $this->provider->delete($this->testStoredFile);
    }

    #[Test]
    public function shouldGenerateUrl(): void
    {
        $this->slugger->expects($this->never())->method('slug');
        $this->filesystem->expects($this->never())->method('copy');
        $this->filesystem->expects($this->never())->method('remove');

        $result = $this->provider->generateUrl($this->testStoredFile);

        self::assertSame($this->uploadsBaseUrl.'/user-avatar/avatar.png', $result);
    }
}
