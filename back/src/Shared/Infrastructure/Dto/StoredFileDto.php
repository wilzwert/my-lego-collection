<?php

namespace App\Shared\Infrastructure\Dto;

use App\Shared\Domain\Model\StoredFile;
use App\Shared\Infrastructure\Service\StoredFileDtoTransformer;
use App\Shared\Infrastructure\Service\StoredFileUrlTransformer;
use Symfony\Component\ObjectMapper\Attribute\Map;

/**
 * @author Wilhelm Zwertvaegher
 */
#[Map(source: StoredFile::class)]
class StoredFileDto
{

    #[Map(source: 'path', transform: StoredFileUrlTransformer::class)]
    public string $url;

    public function __construct(
        public string $filename,
        public string $extension
    ) {
    }
}
