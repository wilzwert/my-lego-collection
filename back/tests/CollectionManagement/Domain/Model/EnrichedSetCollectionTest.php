<?php

namespace App\Tests\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\EnrichedSet;
use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EnrichedSetCollectionTest extends TestCase
{
    #[Test]
    public function testEnrichedSetCollection(): void
    {
        $collection = new EnrichedSetCollection([
            new EnrichedSet(new ExternalSet('externalId1', 'legoId1', 'BaseSet 1', 100, '', 2008)),
            new EnrichedSet(new ExternalSet('externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009))
        ]);
        $collection->add(new EnrichedSet(new ExternalSet('externalId3', 'legoId3', 'BaseSet 3', 50, '', 2006)));

        $this->assertCount(3, $collection);
        $this->assertInstanceOf(EnrichedSet::class, $collection->get(0));
        $this->assertInstanceOf(EnrichedSet::class, $collection->get(1));
        $this->assertNull($collection->get(3));
        $this->assertEquals('legoId1', $collection->get(0)->getSet()->getLegoId());
    }

    #[Test]
    public function whenPassingWrongType_thenShouldThrowInvalidArgumentException(): void
    {
        $collection = new EnrichedSetCollection([
            new EnrichedSet(new ExternalSet('externalId1', 'legoId1', 'BaseSet 1', 100, '', 2008)),
            new EnrichedSet(new ExternalSet('externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009))
        ]);

        // adding a Part to a SetCollection should not be possible
        $this->expectException(InvalidArgumentException::class);
        // @phpstan-ignore argument.type
        $collection->add(new ExternalPart('partExternalId', 'legoId', 'Part', ''));

        $collection->add(new EnrichedSet(new ExternalSet('externalId3', 'legoId3', 'BaseSet 3', 50, '', 2006)));
        $this->assertCount(3, $collection);
    }
}
