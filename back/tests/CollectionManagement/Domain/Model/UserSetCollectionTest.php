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
    private static array $uuids = [
        'localSetId1' => 'abcd1234-abcd-4bcd-abcd-abcd1234abcd',
        'localSetId2' => 'bbcd1234-abcd-4bcd-abcd-abcd1234abcd',
        'localSetId3' => 'cbcd1234-abcd-4bcd-abcd-abcd1234abcd',
        'userSetId1' => 'dbcd1234-abcd-4bcd-abcd-abcd1234abcd',
        'userSetId2' => 'ebcd1234-abcd-4bcd-abcd-abcd1234abcd',
        'userSetId3' => 'fbcd1234-abcd-4bcd-abcd-abcd1234abcd',
    ];

    #[Test]
    public function testUserSetCollection(): void
    {
        $localSet1 = new Set(Uuid::fromString(self::$uuids['localSetId1']), 'external-123', 'lego-123', 'Star Wars Superstar Destroyer', 1000, '/images/destroyer.png', 2011);
        $localSet2 = new Set(Uuid::fromString(self::$uuids['localSetId2']), 'external-456', 'lego-456', 'Star Wars Death Star', 1500, '/images/death.png', 2010);
        $localSet3 = new Set(Uuid::fromString(self::$uuids['localSetId3']), 'external-789', 'lego-789', 'Star Wars Cantina', 2000, '/images/cantina.png', 2014);

        $collection = new UserSetCollection([
            new UserSet(Uuid::fromString(self::$uuids['userSetId1']), $localSet1),
            new UserSet(Uuid::fromString(self::$uuids['userSetId2']), $localSet2)
        ]);
        $collection->add(new UserSet(Uuid::fromString(self::$uuids['userSetId3']), $localSet3));

        self::assertCount(3, $collection);
        self::assertInstanceOf(UserSet::class, $collection->get(0));
        self::assertInstanceOf(UserSet::class, $collection->get(1));
        self::assertInstanceOf(UserSet::class, $collection->get(2));
        self::assertNull($collection->get(3));
        self::assertEquals($localSet1, $collection->get(0)->getLocalSet());
        self::assertEquals(Uuid::fromString(self::$uuids['userSetId1']), $collection->get(0)->getId());
    }

    #[Test]
    public function whenPassingWrongType_thenShouldThrowInvalidArgumentException(): void
    {
        $localSet1 = new Set(Uuid::fromString(self::$uuids['localSetId1']), 'external-123', 'lego-123', 'Star Wars Superstar Destroyer', 1000, '/images/destroyer.png', 2011);
        $localSet2 = new Set(Uuid::fromString(self::$uuids['localSetId2']), 'external-456', 'lego-456', 'Star Wars Death Star', 1500, '/images/death.png', 2010);
        $localSet3 = new Set(Uuid::fromString(self::$uuids['localSetId3']), 'external-789', 'lego-789', 'Star Wars Cantina', 2000, '/images/cantina.png', 2014);

        $collection = new UserSetCollection([
            new UserSet(Uuid::fromString(self::$uuids['userSetId1']), $localSet1),
            new UserSet(Uuid::fromString(self::$uuids['userSetId2']), $localSet2)
        ]);

        // adding a ExternalPart to a UserSetCollection should not be possible
        $this->expectException(InvalidArgumentException::class);
        // @phpstan-ignore argument.type
        $collection->add(new ExternalPart('partExternalId', 'legoId', 'Part', ''));

        $collection->add(new UserSet(Uuid::fromString(self::$uuids['userSetId3']), $localSet3));
        self::assertCount(3, $collection);
    }
}
