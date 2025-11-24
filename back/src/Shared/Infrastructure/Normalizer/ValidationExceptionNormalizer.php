<?php

namespace App\Shared\Infrastructure\Normalizer;

use App\Shared\Domain\Exception\ValidationException;
use InvalidArgumentException;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;

#[AutoconfigureTag('app.exception_normalizer')]
class ValidationExceptionNormalizer extends ExceptionNormalizer
{

    use ClockAwareTrait;

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array<string, mixed> $context
     * @return bool
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ValidationException;
    }


    /**
     * @param Throwable $throwable
     * @return array<string, array<string, string[]|int[]>>
     */
    protected function normalizeErrors(Throwable $throwable): array
    {
        if (!$throwable instanceof ValidationException) {
            throw new InvalidArgumentException();
        }

        $errors = $throwable->getValidationErrors()->getErrors();

        $errorsAsArray = [];
        foreach ($errors as $field => $validationErrors) {
            $errorsAsArray[$field] = [];
            foreach ($validationErrors as $validationError) {
                $errorsAsArray[$field][$validationError->code()->getCode()] = $validationError->details();
            }
        }

        return $errorsAsArray;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [ValidationException::class => true];
    }

    #[\Override]
    protected function getErrorCode(Throwable $throwable): string
    {
        return 'validation-error';
    }

    #[\Override]
    protected function getStatus(Throwable $throwable): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }
}
