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
        $id = Uuid::fromString('dec59684-bdef-4a63-bad4-591c35540fa8');
        $set = new Set($id, 'external-123', 'lego-456', 'Star Wars Superstar Destroyer', 1000, '/images/destroyer.png', 2011);

        self::assertEquals($id, $set->getId());
    }
}
