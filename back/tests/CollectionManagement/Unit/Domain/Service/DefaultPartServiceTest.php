<?php

namespace App\Tests\CollectionManagement\Unit\Domain\Service;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Service\DefaultPartService;
use App\CollectionManagement\Domain\Service\LegoDataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class DefaultPartServiceTest extends TestCase
{
    #[Test]
    public function findPartsDelegatesCallToLegoDataProvider(): void
    {
        $search = 'wheel';

        $expectedCollection = new PartCollection([
            new ExternalPart('externalId1', 'legoId1', 'Part 1', ''),
            new ExternalPart('externalId2', 'legoId2', 'Part 2', ''),
        ]);

        $legoDataProvider = $this->createMock(LegoDataProvider::class);
        $legoDataProvider
            ->expects($this->once())
            ->method('findParts')
            ->with($search)
            ->willReturn($expectedCollection);

        $service = new DefaultPartService($legoDataProvider);

        $result = $service->findParts($search);

        self::assertSame($expectedCollection, $result);
    }
}

