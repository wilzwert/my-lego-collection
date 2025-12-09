<?php

namespace App\Tests\CollectionManagement\Unit\Domain\Model;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\UserSetCollection;
use App\Shared\Domain\Model\EntityId;
use App\Tests\CollectionManagement\Utilities\CollectionManagementTestsUtility;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserSetCollectionTest extends TestCase
{
    private static array $uuids = [
        'userId1' => 'aacd1234-abcd-4bcd-abcd-abcd1234abcd',
        'userSetId1' => 'dbcd1234-abcd-4bcd-abcd-abcd1234abcd',
        'userSetId2' => 'ebcd1234-abcd-4bcd-abcd-abcd1234abcd',
        'userSetId3' => 'fbcd1234-abcd-4bcd-abcd-abcd1234abcd',
    ];

    #[Test]
    public function testUserSetCollection(): void
    {
        $localSet1 = CollectionManagementTestsUtility::generateSet();
        $localSet2 = CollectionManagementTestsUtility::generateSet();
        $localSet3 = CollectionManagementTestsUtility::generateSet();

        $collection = new UserSetCollection([
            CollectionManagementTestsUtility::generateUserSet($localSet1, self::$uuids['userSetId1'], self::$uuids['userId1']),
            CollectionManagementTestsUtility::generateUserSet($localSet2, self::$uuids['userSetId2'], self::$uuids['userId1']),
        ]);
        $collection->add(CollectionManagementTestsUtility::generateUserSet($localSet3, self::$uuids['userSetId3'], self::$uuids['userId1']));

        self::assertCount(3, $collection);
        self::assertInstanceOf(UserSet::class, $collection->get(0));
        self::assertInstanceOf(UserSet::class, $collection->get(1));
        self::assertInstanceOf(UserSet::class, $collection->get(2));
        self::assertNull($collection->get(3));
        self::assertEquals($localSet1->getId(), $collection->get(0)->getSetId());
        self::assertEquals(EntityId::fromString(self::$uuids['userSetId1']), $collection->get(0)->getId());
    }

    #[Test]
    public function whenPassingWrongType_thenShouldThrowInvalidArgumentException(): void
    {
        $localSet1 = CollectionManagementTestsUtility::generateSet();
        $localSet2 = CollectionManagementTestsUtility::generateSet();
        $localSet3 = CollectionManagementTestsUtility::generateSet();

        $collection = new UserSetCollection([
            CollectionManagementTestsUtility::generateUserSet($localSet1, self::$uuids['userSetId1'], self::$uuids['userId1']),
            CollectionManagementTestsUtility::generateUserSet($localSet2, self::$uuids['userSetId2'], self::$uuids['userId1']),
        ]);

        // adding a ExternalPart to a UserSetCollection should not be possible
        $this->expectException(InvalidArgumentException::class);
        // @phpstan-ignore argument.type
        $collection->add(new ExternalPart('partExternalId', 'legoId', 'Part', ''));

        $collection->add(CollectionManagementTestsUtility::generateUserSet($localSet3, self::$uuids['userSetId3'], self::$uuids['userId1']));
        self::assertCount(3, $collection);
    }
}
