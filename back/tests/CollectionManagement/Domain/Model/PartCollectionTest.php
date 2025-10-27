<?php

namespace App\Tests\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\PartCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PartCollectionTest extends TestCase
{
    #[Test]
    public function testExternalPartCollection()
    {
        $collection = new PartCollection([
           new ExternalPart('externalId1', 'legoId1', 'BaseSet 1', 100, '', 2008),
           new ExternalPart('externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009),
        ]);
        $collection->add(new ExternalPart('externalId3', 'legoId3', 'BaseSet 3', 50, '', 2006),);

        $this->assertCount(3, $collection);
        $this->assertInstanceOf(ExternalPart::class, $collection->get(0));
        $this->assertInstanceOf(ExternalPart::class, $collection->get(1));
        $this->assertNull($collection->get(3));
        $this->assertEquals('legoId1', $collection->get(0)->getLegoId());
    }

    #[Test]
    public function whenPassingWrongType_thenShouldThrowInvalidArgumentException()
    {
        $collection = new PartCollection([
            new ExternalPart('externalId1', 'legoId1', 'Part 1', '', 2008),
            new ExternalPart('externalId2', 'legoId2', 'Part 2', '', 2009),
        ]);

        // adding a Part to a PartCollection should not be possible
        $this->expectException(\InvalidArgumentException::class);
        $collection->add(new ExternalSet('exernalId', 'legoId','Set', 100, '', 2006));

        $collection->add(new ExternalPart('externalId3', 'legoId3', 'BaseSet 3', 50, '', 2006),);
        $this->assertCount(3, $collection);
    }
}
