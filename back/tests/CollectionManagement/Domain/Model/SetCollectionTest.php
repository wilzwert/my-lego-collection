<?php

namespace App\Tests\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\SetCollection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SetCollectionTest extends TestCase
{
    #[Test]
    public function testSetCollection(): void
    {
        $collection = new SetCollection([
           new ExternalSet('externalId1', 'legoId1', 'BaseSet 1', 100, '', 2008),
           new ExternalSet('externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009),
        ]);
        $collection->add(new ExternalSet('externalId3', 'legoId3', 'BaseSet 3', 50, '', 2006));

        self::assertCount(3, $collection);
        self::assertInstanceOf(ExternalSet::class, $collection->get(0));
        self::assertInstanceOf(ExternalSet::class, $collection->get(1));
        self::assertNull($collection->get(3));
        self::assertEquals('legoId1', $collection->get(0)->getLegoId());
    }

    #[Test]
    public function whenPassingWrongType_thenShouldThrowInvalidArgumentException(): void
    {
        $collection = new SetCollection([
            new ExternalSet('externalId1', 'legoId1', 'BaseSet 1', 100, '', 2008),
            new ExternalSet('externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009),
        ]);

        // adding a Part to a SetCollection should not be possible
        $this->expectException(InvalidArgumentException::class);
        // @phpstan-ignore argument.type
        $collection->add(new ExternalPart('partExternalId', 'legoId', 'Part', ''));

        $collection->add(new ExternalSet('externalId3', 'legoId3', 'BaseSet 3', 50, '', 2006));
        self::assertCount(3, $collection);
    }
}
