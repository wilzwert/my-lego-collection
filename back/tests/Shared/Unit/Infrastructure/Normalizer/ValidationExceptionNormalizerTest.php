<?php

namespace App\Tests\Shared\Unit\Infrastructure\Normalizer;

use App\Shared\Domain\Exception\ErrorCode;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Domain\Validation\ValidationError;
use App\Shared\Domain\Validation\ValidationErrors;
use App\Shared\Infrastructure\Normalizer\ValidationExceptionNormalizer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Wilhelm Zwertvaegher
 */
class ValidationExceptionNormalizerTest extends TestCase
{
    use ClockSensitiveTrait;

    #[Test]
    public function shouldNormalizeComplexErrors(): void
    {
        $clock = static::mockTime(new \DateTimeImmutable('2025-11-07 13:10:00'));
        $expectedErrors = [
            'field' => [
                'INVALID_EMAIL' => ['invalid_email' => 'Email is invalid 2'],
                'UNKNOWN_ERROR' => ['unknown' => 'Unknown error'],
            ],
            'field2' => [
                'INVALID_UUID' => ['uuid' => 'Invalid UUID'],
            ],
            'field3' => [
                'INVALID_URL' => []
            ]
        ];
        $expectedNormalizedException = [
            'timestamp' => new DatePoint('2025-11-07 13:10:00')->format(\DateTimeInterface::RFC3339_EXTENDED),
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'error' => 'validation-error',
            'message' => 'VALIDATION_FAILED',
            'errors' => $expectedErrors,
        ];


        $validationErrors = new ValidationErrors();
        $validationErrors->add(new ValidationError('field', ErrorCode::INVALID_EMAIL, ['invalid_email' => 'Email is invalid']));
        $validationErrors->add(new ValidationError('field', ErrorCode::INVALID_EMAIL, ['invalid_email' => 'Email is invalid 2']));
        $validationErrors->add(new ValidationError('field', ErrorCode::UNKNOWN_ERROR, ['unknown' => 'Unknown error']));
        $validationErrors->add(new ValidationError('field2', ErrorCode::INVALID_UUID, ['uuid' => 'Invalid UUID']));
        $validationErrors->add(new ValidationError('field3', ErrorCode::INVALID_URL));
        $exception = new ValidationException($validationErrors);
        $normalizer = new ValidationExceptionNormalizer();
        $normalizer->setClock($clock);
        $normalized = $normalizer->normalize($exception);

        self::assertEquals($expectedNormalizedException, $normalized);
    }
}
