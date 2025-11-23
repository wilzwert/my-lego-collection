<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Model\StoredFile;
use App\Shared\Infrastructure\Dto\StoredFileDto;
use App\Shared\Infrastructure\Persistence\Doctrine\Entity\DoctrineStoredFile;
use Doctrine\ORM\Internal\StronglyConnectedComponents;
use Symfony\Component\ObjectMapper\ObjectMapper;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @author Wilhelm Zwertvaegher
 * @implements  TransformCallableInterface<StoredFile, StoredFileDto>
 */
readonly class StoredFileDtoTransformer implements TransformCallableInterface
{

    public function __construct(
        private ObjectMapperInterface $objectMapper

    ) {
    }

    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        return null === $value ? null : $this->objectMapper->map($value, StoredFileDto::class);
    }
}
