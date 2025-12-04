<?php

namespace App\Tests\Shared\Integration\Infrastructure\Service;

/**
 * @author Wilhelm Zwertvaegher
 */


use App\Shared\Domain\Model\TempFile;
use App\Shared\Infrastructure\Service\Base64FileDecoder;
use App\Tests\Traits\TestResourcesTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mime\MimeTypesInterface;

final class Base64FileDecoderIT extends KernelTestCase
{
    use TestResourcesTrait;

    private Filesystem $filesystem;
    private MimeTypes $mimeTypes;
    private Base64FileDecoder $decoder;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->filesystem = $container->get(Filesystem::class);
        $this->mimeTypes = $container->get(MimeTypesInterface::class);
        $this->decoder = $container->get(Base64FileDecoder::class);
    }

    #[Test]
    public function shouldDecodeValideBase64AndCreateTempFile(): void
    {
        // a valid base64 encoded png
        $imageData = file_get_contents($this->getTestResourcePath('files/lego_png_base64.txt'));
        $originalFilename = 'lego.png';

        // when: decoding to temp file
        $tempFile = $this->decoder->decodeToTempFile($imageData, $originalFilename);

        // then: we expect a valid TempFile object
        self::assertInstanceOf(TempFile::class, $tempFile);
        self::assertFileExists($tempFile->getPath());

        // file size should equal the original image size
        self::assertEquals(filesize($this->getTestResourcePath('files/lego.png')), filesize($tempFile->getPath()));

        // mime type must match png
        self::assertSame('image/png', $tempFile->getMime());
        self::assertSame('png', $tempFile->getExtension());
        self::assertSame($originalFilename, $tempFile->getOriginalFilename());

        // cleanup
        $this->filesystem->remove($tempFile->getPath());
        self::assertFileDoesNotExist($tempFile->getPath());
    }

    #[Test]
    public function shouldThrowsOnInvalidBase64(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid Base64');

        $this->decoder->decodeToTempFile('@@not_base64@@', 'test.png');
    }

    #[Test]
    public function shouldThrowOnMimeMismatch(): void
    {
        // given: a PNG content but .txt extension
        $imageData = base64_encode(hex2bin(
            '89504E470D0A1A0A0000000D4948445200000001000000010806000000' .
            '1F15C4890000000A49444154789C6360000002000100FFFF03000006000557BF' .
            'A60000000049454E44AE426082'
        ));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/does not match its extension/');

        $this->decoder->decodeToTempFile($imageData, 'test.txt');
    }
}
