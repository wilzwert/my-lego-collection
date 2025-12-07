<?php
declare(strict_types=1);

namespace App\Tests\CollectionManagement\Unit\Domain\Model;

use App\CollectionManagement\Domain\Model\BaseSet;
use App\CollectionManagement\Domain\Model\EnrichedSet;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\Shared\Domain\Model\EntityId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class EnrichedSetTest extends TestCase
{
    private function createConcreteSet(): BaseSet
    {
        // Concrete anonymous class used to get a testable instance of the abstract BasePart class
        return new readonly class('external-123', 'lego-456', 'Star Wars Superstar Destroyer', 1000, '/images/destroyer.png', 2011) extends BaseSet {
        };
    }

    #[Test]
    public function getSet_shouldReturnExpectedBaseSet(): void
    {
        $set = $this->createConcreteSet();
        $enrichedSet = new EnrichedSet($set);

        self::assertSame($set, $enrichedSet->getSet());
    }

    #[Test]
    public function getUserSet_shouldReturnNull(): void
    {
        $set = $this->createConcreteSet();
        $enrichedSet = new EnrichedSet($set);

        self::assertNull($enrichedSet->getUserSet());
    }

    #[Test]
    public function getUserSet_shouldReturnExpectedValue(): void
    {
        $set = $this->createConcreteSet();
        $localSet = new Set(EntityId::fromString('abcd1234-abcd-4bcd-abcd-abcd1234abcd'), 'external-123', 'lego-456', 'Star Wars Superstar Destroyer', 1000, '/images/destroyer.png', 2011);
        $userSet = new UserSet(EntityId::fromString('bbcd1234-abcd-4bcd-abcd-abcd1234abcd'), EntityId::fromString('cbcd1234-abcd-4bcd-abcd-abcd1234abcd'), $localSet);
        $enrichedSet = new EnrichedSet($set, $userSet);

        self::assertSame($userSet, $enrichedSet->getUserSet());
    }


}
