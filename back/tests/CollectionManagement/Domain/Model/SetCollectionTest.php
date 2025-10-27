<?php

namespace App\Tests\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\SetCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SetCollectionTest extends TestCase
{
    #[Test]
    public function testExternalSetCollection()
    {
        $collection = new SetCollection([
           new ExternalSet('externalId1', 'legoId1', 'BaseSet 1', 100, '', 2008),
           new ExternalSet('externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009),
        ]);
        $collection->add(new ExternalSet('externalId3', 'legoId3', 'BaseSet 3', 50, '', 2006),);

        $this->assertCount(3, $collection);
        $this->assertInstanceOf(ExternalSet::class, $collection->get(0));
        $this->assertInstanceOf(ExternalSet::class, $collection->get(1));
        $this->assertNull($collection->get(3));
        $this->assertEquals('legoId1', $collection->get(0)->getLegoId());
    }

    #[Test]
    public function whenPassingWrongType_thenShouldThrowInvalidArgumentException()
    {
        $collection = new SetCollection([
            new ExternalSet('externalId1', 'legoId1', 'BaseSet 1', 100, '', 2008),
            new ExternalSet('externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009),
        ]);

        // adding a Part to a SetCollection should not be possible
        $this->expectException(\InvalidArgumentException::class);
        $collection->add(new ExternalPart('partExernalId', 'legoId','Part', ''));

        $collection->add(new ExternalSet('externalId3', 'legoId3', 'BaseSet 3', 50, '', 2006),);
        $this->assertCount(3, $collection);
    }
}
