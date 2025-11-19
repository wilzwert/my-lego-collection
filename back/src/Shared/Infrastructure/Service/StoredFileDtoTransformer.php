<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Infrastructure\Dto\StoredFileDto;
use Doctrine\ORM\Internal\StronglyConnectedComponents;
use Symfony\Component\ObjectMapper\ObjectMapper;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
class StoredFileDtoTransformer implements TransformCallableInterface
{

    public function __construct(
        private readonly ObjectMapperInterface $objectMapper

    ) {
    }

    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        return null === $value ? null : $this->objectMapper->map($value, StoredFileDto::class);
    }
}
