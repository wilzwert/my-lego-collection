<?php
declare(strict_types=1);

namespace App\Tests\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\BasePart;
use App\CollectionManagement\Domain\Model\BaseSet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class BaseSetTest extends TestCase
{
    private function createConcreteSet(): BaseSet
    {
        // Concrete anonymous class used to get a testable instance of the abstract BasePart class
        return new readonly class('external-123', 'lego-456', 'Star Wars Superstar Destroyer', 1000, '/images/destroyer.png', 2011) extends BaseSet {
        };
    }

    #[Test]
    public function getExternalId_shouldReturnExpectedValue(): void
    {
        $set = $this->createConcreteSet();

        self::assertSame('external-123', $set->getExternalId());
    }

    #[Test]
    public function getLegoId_shouldReturnExpectedValue(): void
    {
        $set = $this->createConcreteSet();

        self::assertSame('lego-456', $set->getLegoId());
    }

    #[Test]
    public function getName_shouldReturnExpectedValue(): void
    {
        $set = $this->createConcreteSet();

        self::assertSame('Star Wars Superstar Destroyer', $set->getName());
    }

    #[Test]
    public function getPartCount_shouldReturnExpectedValue(): void
    {
        $set = $this->createConcreteSet();

        self::assertSame(1000, $set->getPartCount());
    }

    #[Test]
    public function getImagePath_shouldReturnExpectedValue(): void
    {
        $set = $this->createConcreteSet();

        self::assertSame('/images/destroyer.png', $set->getImagePath());
    }

    #[Test]
    public function getProductionYear_shouldReturnExpectedValue(): void
    {
        $set = $this->createConcreteSet();

        self::assertSame(2011, $set->getProductionYear());
    }
}
