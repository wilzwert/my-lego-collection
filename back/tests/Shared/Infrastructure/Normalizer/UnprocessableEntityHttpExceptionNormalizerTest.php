<?php

namespace App\Tests\Shared\Infrastructure\Normalizer;

use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Infrastructure\Normalizer\DomainExceptionNormalizer;
use App\Shared\Infrastructure\Normalizer\UnprocessableEntityHttpExceptionNormalizer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @author Wilhelm Zwertvaegher
 */
class UnprocessableEntityHttpExceptionNormalizerTest extends TestCase
{
    use ClockSensitiveTrait;

    private UnprocessableEntityHttpExceptionNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new UnprocessableEntityHttpExceptionNormalizer();
    }

    #[Test]
    public function shouldSupportNormalization(): void
    {
        $exception = new UnprocessableEntityHttpException('Invalid');
        self::assertTrue($this->normalizer->supportsNormalization($exception));
    }

    #[Test]
    public function shouldNotSupportNormalization(): void
    {
        $exception = new \InvalidArgumentException('Invalid');
        self::assertFalse($this->normalizer->supportsNormalization($exception));
    }
    #[Test]
    public function shouldFetSupportedTypes(): void
    {
        self::assertSame(
            [UnprocessableEntityHttpException::class => true],
            $this->normalizer->getSupportedTypes(null)
        );
    }

    #[Test]
    public function shouldNormalizeUnprocessableEntityHttpException(): void
    {
        $clock = static::mockTime(new \DateTimeImmutable('2025-11-07 13:10:00'));
        $expectedErrors = [
            'email' => [
                'INVALID_FORMAT_ERROR' => ['invalid_format_error' => 'Invalid email format']
            ],
            'userId' => [
                'INVALID_CHARACTERS_ERROR' => ['invalid_characters_error' => 'Invalid UUID'],
            ]
        ];



        $expectedNormalizedException = [
            'timestamp' => new DatePoint('2025-11-07 13:10:00')->format(\DateTimeInterface::RFC3339_EXTENDED),
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'error' => 'validation-error',
            'message' => 'Validation failed',
            'errors' => $expectedErrors,
        ];
        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'Invalid email format',
                messageTemplate: '',
                parameters: [],
                root: null,
                propertyPath: 'email',
                invalidValue: 'bademail',
                code: Email::INVALID_FORMAT_ERROR,
                constraint: new Email()
            ),
            new ConstraintViolation(
                message: 'Invalid UUID',
                messageTemplate: '',
                parameters: [],
                root: null,
                propertyPath: 'userId',
                invalidValue: 'not-a-uuid',
                code: Uuid::INVALID_CHARACTERS_ERROR,
                constraint: new Uuid()
            ),
        ]);

        $validationFailed = new ValidationFailedException(new \stdClass(), $violations);
        $httpException = new UnprocessableEntityHttpException('Validation failed', $validationFailed);


        self::assertEquals($expectedNormalizedException, $this->normalizer->normalize($httpException));
    }
}
