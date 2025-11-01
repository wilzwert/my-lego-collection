<?php

namespace App\Tests\Shared\Domain;

use App\Shared\Domain\Uuid;
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
        $uuid = Uuid::fromString('123e4567-e89b-12d3-a456-426614174000');

        $this->assertSame('123e4567-e89b-12d3-a456-426614174000', (string) $uuid);
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
