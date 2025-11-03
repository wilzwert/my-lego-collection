<?php
declare(strict_types=1);

namespace App\Tests\CollectionManagement\Domain\Model\Local;

use App\CollectionManagement\Domain\Model\Local\Set;
use App\Shared\Domain\Model\Uuid;
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
        $id = Uuid::fromString('abcd1234-abcd-4bcd-abcd-abcd1234abcd');
        $set = new Set($id, 'external-123', 'lego-456', 'Star Wars Superstar Destroyer', 1000, '/images/destroyer.png', 2011);

        self::assertEquals($id, $set->getId());
    }
}
