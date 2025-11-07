<?php

namespace App\Tests\Shared\Infrastructure\Normalizer;

use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Infrastructure\Normalizer\DomainExceptionNormalizer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Wilhelm Zwertvaegher
 */
class DomainExceptionNormalizerTest extends TestCase
{
    use ClockSensitiveTrait;

    #[Test]
    public function shouldNormalizeEntityAlreadyExistsException(): void
    {
        $clock = static::mockTime(new \DateTimeImmutable('2025-11-07 13:10:00'));
        $expectedNormalizedException = [
            'timestamp' => '2025-11-07T13:10:00.000+01:00',
            'status' => Response::HTTP_CONFLICT,
            'error' => 'entity-exists',
            'message' => 'Identity already exists',
            'errors' => [],
        ];

        $exception = new EntityAlreadyExistsException('Identity already exists');
        $normalizer = new DomainExceptionNormalizer();
        $normalizer->setClock($clock);
        $normalized = $normalizer->normalize($exception);

        $this->assertEquals($expectedNormalizedException, $normalized);

        var_dump($normalized);
    }
}
