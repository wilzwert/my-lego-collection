<?php

namespace App\Shared\Infrastructure\Normalizer;

use App\Shared\Domain\Exception\ErrorCode;
use InvalidArgumentException;
use Symfony\Component\Clock\ClockAwareTrait;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

#[AutoconfigureTag('app.exception_normalizer')]
class UnprocessableEntityHttpExceptionNormalizer extends ExceptionNormalizer
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
        return $data instanceof UnprocessableEntityHttpException;
    }

    /**
     * @param Throwable $throwable
     * @return array<string, array<string, string[]|int[]>>
     */
    protected function normalizeErrors(Throwable $throwable): array
    {
        if (!$throwable instanceof UnprocessableEntityHttpException) {
            throw new InvalidArgumentException();
        }

        /**
         * @var ValidationFailedException $validationFailedException
         */
        $validationFailedException = $throwable->getPrevious();

        $errorsAsArray = [];
        foreach ($validationFailedException->getViolations() as $violation) {
            $propertyPath = $violation->getPropertyPath();
            /** @var class-string<Constraint> $constraintClass */
            $constraintClass = $violation->getConstraint() ? $violation->getConstraint()::class : null;
            $detailCode = $constraintClass ? $constraintClass::getErrorName($violation->getCode()) : ErrorCode::UNKNOWN_ERROR->getCode();
            if (!isset($errorsAsArray[$propertyPath])) {
                $errorsAsArray[$propertyPath] = [];
            }
            if (!isset($errorsAsArray[$propertyPath][$detailCode])) {
                $errorsAsArray[$propertyPath][$detailCode] = [];
            }
            $errorsAsArray[$propertyPath][$detailCode][strtolower($detailCode)] = $violation->getMessage();
        }
        return $errorsAsArray;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [UnprocessableEntityHttpException::class => true];
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
