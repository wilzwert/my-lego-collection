<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Model\StoredFile;
use App\Shared\Infrastructure\Dto\StoredFileDto;
use Doctrine\ORM\Internal\StronglyConnectedComponents;
use Symfony\Component\ObjectMapper\ObjectMapper;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
class StoredFileUrlTransformer implements TransformCallableInterface
{

    public function __construct(
        private readonly FileStorageProvider $fileStorageProvider
    ) {
    }

    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        return null === $value || !$source instanceof StoredFile ? null : $this->fileStorageProvider->generateUrl($source);
    }
}
