<?php
declare(strict_types=1);

namespace App\Tests\CollectionManagement\Unit\Domain\Model\Local;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\Shared\Domain\Model\EntityId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class SetTest extends TestCase
{
    #[Test]
    public function getId_shouldReturnExpectedValue(): void
    {
        $id = EntityId::fromString('abcd1234-abcd-4bcd-abcd-abcd1234abcd');
        $set = new Set(
            $id,
            'external-123',
            'lego-456',
            'Star Wars Superstar Destroyer',
            1000,
            '/images/destroyer.png',
            2011,
SetCreationStatus::COMPLETED,
            new \DateTimeImmutable('2025-11-10T12:00:00'),
            new \DateTimeImmutable('2025-11-10T14:00:00')
        );

        self::assertEquals($id, $set->getId());
    }
}
