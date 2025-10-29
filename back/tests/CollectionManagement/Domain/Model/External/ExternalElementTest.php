<?php

namespace App\Tests\CollectionManagement\Domain\Model\External;

use App\CollectionManagement\Domain\Model\External\ExternalElement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class ExternalElementTest extends TestCase
{
    private function createExternalElement(): ExternalElement
    {
        return new ExternalElement(
            'external-001',
            'lego-1234',
            'part-5678',
            '/images/lego-1234.png',
            'color-89',
            'Bright Red'
        );
    }

    #[Test]
    public function getExternalId_shouldReturnExpectedValue(): void
    {
        $element = $this->createExternalElement();

        self::assertSame('external-001', $element->getExternalId());
    }

    #[Test]
    public function getLegoId_shouldReturnExpectedValue(): void
    {
        $element = $this->createExternalElement();

        self::assertSame('lego-1234', $element->getLegoId());
    }

    #[Test]
    public function getExternalPartId_shouldReturnExpectedValue(): void
    {
        $element = $this->createExternalElement();

        self::assertSame('part-5678', $element->getExternalPartId());
    }

    #[Test]
    public function getImagePath_shouldReturnExpectedValue(): void
    {
        $element = $this->createExternalElement();

        self::assertSame('/images/lego-1234.png', $element->getImagePath());
    }

    #[Test]
    public function getExternalColorId_shouldReturnExpectedValue(): void
    {
        $element = $this->createExternalElement();

        self::assertSame('color-89', $element->getExternalColorId());
    }

    #[Test]
    public function getColorName_shouldReturnExpectedValue(): void
    {
        $element = $this->createExternalElement();

        self::assertSame('Bright Red', $element->getColorName());
    }
}
