<?php

namespace App\Tests\Shared\Domain\Model;

use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Domain\Model\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class UuidTest extends TestCase
{

    #[Test]
    public function fromString_shouldStoreValue(): void
    {
        $uuid = Uuid::fromString('dec59684-bdef-4a63-bad4-591c35540fa8');

        $this->assertSame('dec59684-bdef-4a63-bad4-591c35540fa8', (string) $uuid);
    }

    #[Test]
    public function fromString_shouldThrowValidation_whenUuidInvalid(): void
    {
        $this->expectException(ValidationException::class);
        $uuid = Uuid::fromString('123e4567-e89b-12d3-a456-426614174000');

    }

    #[Test]
    public function generate_shouldProduceValidV4Uuid(): void
    {
        $uuid = (string) Uuid::generate();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $uuid,
            'Should be a valid UUID v4'
        );

        $this->assertNotSame((string) Uuid::generate(), (string) Uuid::generate());
    }
}
