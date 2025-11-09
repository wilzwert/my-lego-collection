<?php

namespace App\Shared\Infrastructure\Service;

/**
 * @author Wilhelm Zwertvaegher
 */
namespace App\Shared\Infrastructure\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mime\MimeTypes;

final class Base64FileDecoder
{
    public function __construct(private Filesystem $filesystem, private MimeTypes $mimeTypes) {}

    public function decodeToTempFile(string $base64, string $originalFilename): string
    {

        $tempPath = $this->filesystem->tempnam(sys_get_temp_dir(), uniqid('upload_', true));

        $binary = base64_decode($base64, true);
        if ($binary === false) {
            throw new \RuntimeException('Invalid Base64');
        }

        $this->filesystem->dumpFile($tempPath, $binary);

        $mime = $this->mimeTypes->guessMimeType($tempPath);

        $expectedExtension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $expectedMimes = $this->mimeTypes->getMimeTypes($expectedExtension);

        if (!in_array($mime, $expectedMimes, true)) {
            throw new \RuntimeException(sprintf(
                'File contents does not match its extension (%s vs %s)',
                $mime,
                $expectedExtension
            ));
        }

        return $tempPath;
    }
}

