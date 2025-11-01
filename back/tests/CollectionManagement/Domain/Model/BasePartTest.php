<?php
declare(strict_types=1);

namespace App\Tests\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\BasePart;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class BasePartTest extends TestCase
{
    private function createConcretePart(): BasePart
    {
        // Concrete anonymous class used to get a testable instance of the abstract BasePart class
        return new readonly class('external-123', 'lego-456', 'Brick 2x4', '/images/brick-2x4.png') extends BasePart {
        };
    }

    #[Test]
    public function getExternalId_shouldReturnExpectedValue(): void
    {
        $part = $this->createConcretePart();

        self::assertSame('external-123', $part->getExternalId());
    }

    #[Test]
    public function getLegoId_shouldReturnExpectedValue(): void
    {
        $part = $this->createConcretePart();

        self::assertSame('lego-456', $part->getLegoId());
    }

    #[Test]
    public function getName_shouldReturnExpectedValue(): void
    {
        $part = $this->createConcretePart();

        self::assertSame('Brick 2x4', $part->getName());
    }

    #[Test]
    public function getImagePath_shouldReturnExpectedValue(): void
    {
        $part = $this->createConcretePart();

        self::assertSame('/images/brick-2x4.png', $part->getImagePath());
    }
}
