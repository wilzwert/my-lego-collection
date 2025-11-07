<?php

namespace App\Tests\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\UserSetCollection;
use App\Shared\Domain\Model\Uuid;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserSetCollectionTest extends TestCase
{
    #[Test]
    public function testUserSetCollection(): void
    {
        $localSet1 = new Set(Uuid::fromString('dec59684-bdef-4a63-bad4-591c35540fa8'), 'external-123', 'lego-123', 'Star Wars Superstar Destroyer', 1000, '/images/destroyer.png', 2011);
        $localSet2 = new Set(Uuid::fromString('eec59684-bdef-4a63-bad4-591c35540fa8'), 'external-456', 'lego-456', 'Star Wars Death Star', 1500, '/images/death.png', 2010);
        $localSet3 = new Set(Uuid::fromString('fec59684-bdef-4a63-bad4-591c35540fa8'), 'external-789', 'lego-789', 'Star Wars Cantina', 2000, '/images/cantina.png', 2014);

        $collection = new UserSetCollection([
            new UserSet(Uuid::fromString('aec59684-bdef-4a63-bad4-591c35540fa8'), $localSet1),
            new UserSet(Uuid::fromString('bec59684-bdef-4a63-bad4-591c35540fa8'), $localSet2)
        ]);
        $collection->add(new UserSet(Uuid::fromString('cec59684-bdef-4a63-bad4-591c35540fa8'), $localSet3));

        self::assertCount(3, $collection);
        self::assertInstanceOf(UserSet::class, $collection->get(0));
        self::assertInstanceOf(UserSet::class, $collection->get(1));
        self::assertInstanceOf(UserSet::class, $collection->get(2));
        self::assertNull($collection->get(3));
        self::assertEquals($localSet1, $collection->get(0)->getLocalSet());
        self::assertEquals('aec59684-bdef-4a63-bad4-591c35540fa8', $collection->get(0)->getId()->__toString());
    }

    #[Test]
    public function whenPassingWrongType_thenShouldThrowInvalidArgumentException(): void
    {
        $localSet1 = new Set(Uuid::fromString('dec59684-bdef-4a63-bad4-591c35540fa8'), 'external-123', 'lego-123', 'Star Wars Superstar Destroyer', 1000, '/images/destroyer.png', 2011);
        $localSet2 = new Set(Uuid::fromString('eec59684-bdef-4a63-bad4-591c35540fa8'), 'external-456', 'lego-456', 'Star Wars Death Star', 1500, '/images/death.png', 2010);

        $collection = new UserSetCollection([
            new UserSet(Uuid::fromString('aec59684-bdef-4a63-bad4-591c35540fa8'), $localSet1),
            new UserSet(Uuid::fromString('bec59684-bdef-4a63-bad4-591c35540fa8'), $localSet2)
        ]);

        // adding a ExternalPart to a UserSetCollection should not be possible
        $this->expectException(InvalidArgumentException::class);
        // @phpstan-ignore argument.type
        $collection->add(new ExternalPart('partExternalId', 'legoId', 'Part', ''));
    }
}
