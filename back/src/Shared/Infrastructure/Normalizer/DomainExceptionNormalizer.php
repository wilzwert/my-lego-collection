<?php

namespace App\Shared\Infrastructure\Normalizer;

use App\Shared\Domain\Exception\DomainException;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Exception\FileStorageException;
use App\Shared\Domain\Exception\ValidationException;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag('app.exception_normalizer')]
class DomainExceptionNormalizer extends ExceptionNormalizer
{
    private static array $status_codes = [
        EntityNotFoundException::class => Response::HTTP_NOT_FOUND,
        EntityAlreadyExistsException::class => Response::HTTP_CONFLICT,
        FileStorageException::class => Response::HTTP_INTERNAL_SERVER_ERROR
    ];

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof DomainException;
    }

    protected function normalizeErrors(\Throwable $throwable): array
    {
        if (!$throwable instanceof DomainException) {
            throw new InvalidArgumentException();
        }
        return [];
    }

    /**
     * @param DomainException $throwable
     * @return string
     */
    protected function getErrorCode(\Throwable $throwable): string
    {
        return match ($throwable::class) {
            EntityNotFoundException::class => 'entity-not-found',
            EntityAlreadyExistsException::class => 'entity-exists',
            FileStorageException::class => 'file-storage',
            default => 'internal-error'
        };
    }

    public function getSupportedTypes(?string $format): array
    {
        return [ValidationException::class => true];
    }

    protected function getStatus(\Throwable $throwable): int
    {
        return static::$status_codes[$throwable::class] ?? Response::HTTP_BAD_REQUEST;
    }
}
