<?php

namespace App\Tests\CollectionManagement\Unit\Domain\Model;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\PartCollection;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PartCollectionTest extends TestCase
{
    #[Test]
    public function testPartCollection(): void
    {
        $collection = new PartCollection([
           new ExternalPart('externalId1', 'legoId1', 'Part 1', 'image1'),
           new ExternalPart('externalId2', 'legoId2', 'Part 2', 'image2'),
        ]);
        $collection->add(new ExternalPart('externalId3', 'legoId3', 'Part 3', 'image3'));

        self::assertCount(3, $collection);
        self::assertInstanceOf(ExternalPart::class, $collection->get(0));
        self::assertInstanceOf(ExternalPart::class, $collection->get(1));
        self::assertNull($collection->get(3));
        self::assertEquals('legoId1', $collection->get(0)->getLegoId());
    }

    #[Test]
    public function whenPassingWrongType_thenShouldThrowInvalidArgumentException(): void
    {
        $collection = new PartCollection([
            new ExternalPart('externalId1', 'legoId1', 'Part 1', ''),
            new ExternalPart('externalId2', 'legoId2', 'Part 2', ''),
        ]);

        // adding a ExternalSet to a PartCollection should not be possible
        $this->expectException(InvalidArgumentException::class);
        // @phpstan-ignore argument.type
        $collection->add(new ExternalSet('exernalId', 'legoId', 'Set', 100, '', 2006));

        $collection->add(new ExternalPart('externalId', 'legoId', 'Part', 'part'));
        self::assertCount(3, $collection);
    }
}
