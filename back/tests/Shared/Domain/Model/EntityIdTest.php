<?php

namespace App\Tests\Shared\Domain\Model;

use App\Shared\Domain\Exception\InvalidEntityIdException;
use App\Shared\Domain\Model\EntityId;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class EntityIdTest extends TestCase
{

    #[Test]
    public function fromString_shouldStoreValue(): void
    {
        $uuid = EntityId::fromString('e345818a-1043-4d48-a23a-e7cf30e7d76a');
        $this->assertSame('e345818a-1043-4d48-a23a-e7cf30e7d76a', (string) $uuid);
    }

    public static function invalidUuid(): array
    {
        return [
            ['bad_uuid'],
            ['a1a1a1a1-a1a1-41a1-8a1a-a1a1a1'],
            ['a1a1a1a1-a1a1-51a1-8a1a-a1a1a1a1a1a1'], // third group must start with 4
            ['a1a1a1a1-a1a1-41a1-ca1a-a1a1a1a1a1a1'] // fourth group must start with [89ab]
        ];
    }

    #[Test]
    #[DataProvider('invalidUuid')]
    public function fromString_shouldThrowException($str): void
    {
        $this->expectException(InvalidEntityIdException::class);
        $uuid = EntityId::fromString($str);
    }


    #[Test]
    public function generate_shouldProduceValidV4Uuid(): void
    {
        $uuid = (string) EntityId::generate();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            $uuid,
            'Should be a valid UUID v4'
        );

        $this->assertNotSame((string) EntityId::generate(), (string) EntityId::generate());
    }
}
