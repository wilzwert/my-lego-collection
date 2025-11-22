<?php

namespace App\Tests\CollectionManagement\Domain\Service;


use App\CollectionManagement\Domain\Model\EnrichedSet;
use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Domain\Model\UserSetCollection;
use App\CollectionManagement\Domain\Repository\UserSetRepository;
use App\CollectionManagement\Domain\Service\DefaultSetService;
use App\CollectionManagement\Domain\Service\LegoDataProvider;
use App\Shared\Domain\Model\EntityId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class DefaultSetServiceTest extends TestCase
{
    #[Test]
    public function findSets_shouldReturnEnrichedSetCollectionWithoutUserId(): void
    {
        $expectedExternalSetsCollection = new EnrichedSetCollection([
            new EnrichedSet(new ExternalSet('externalId1', 'legoId1', 'BaseSet 1', 100, '', 2008)),
            new EnrichedSet(new ExternalSet('externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009))
        ]);

        $setsCollection = new SetCollection([
            new ExternalSet('externalId1', 'legoId1', 'BaseSet 1', 100, '', 2008),
            new ExternalSet('externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009)
        ]);

        $legoDataProvider = $this->createMock(LegoDataProvider::class);
        $legoDataProvider
            ->expects(self::once())
            ->method('findSets')
            ->with('space')
            ->willReturn($setsCollection);

        $userSetRepository = $this->createMock(UserSetRepository::class);

        $service = new DefaultSetService($legoDataProvider, $userSetRepository);

        $result = $service->findSets('space', null);

        $items = $result->toArray();
        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(EnrichedSet::class, $items);
        self::assertEquals($expectedExternalSetsCollection, $result);
    }

    #[Test]
    public function findSets_shouldReturnEnrichedSetCollectionWithUserData(): void
    {
        $userId = EntityId::fromString('abcd1234-abcd-4bcd-abcd-abcd1234abcd');
        $userSetId = EntityId::fromString('bbcd1234-abcd-4bcd-abcd-abcd1234abcd');
        $localSetId = EntityId::fromString('cbcd1234-abcd-4bcd-abcd-abcd1234abcd');

        // External sets
        $externalSetsCollection = new SetCollection([
            new ExternalSet('externalId1', 'legoId1', 'BaseSet 1', 100, '', 2008),
            new ExternalSet('externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009)
        ]);

        // Local user sets collection
        $userSet = new UserSet($userSetId, $userId, new Set($localSetId, 'externalId2', 'legoId2', 'BaseSet 2', 200, '', 2009));
        $userSetCollection = new UserSetCollection([$userSet]);

        // Mocks LegoDataProvider + UserSetRepository
        $legoDataProvider = $this->createMock(LegoDataProvider::class);
        $legoDataProvider
            ->expects(self::once())
            ->method('findSets')
            ->with('castle')
            ->willReturn($externalSetsCollection);

        $userSetRepository = $this->createMock(UserSetRepository::class);
        $userSetRepository
            ->expects(self::once())
            ->method('findByUserAndExternalIds')
            ->with($userId, ['externalId1', 'externalId2'])
            ->willReturn($userSetCollection);

        $service = new DefaultSetService($legoDataProvider, $userSetRepository);

        $result = $service->findSets('castle', $userId);

        $items = $result->toArray();
        self::assertCount(2, $items);
        self::assertContainsOnlyInstancesOf(EnrichedSet::class, $items);

        // second ExternalSet should be enriched with UserSet
        self::assertNull($items[0]->getUserSet());
        self::assertSame('externalId2', $items[1]->getSet()->getExternalId());
        self::assertSame($userSet, $items[1]->getUserSet());
        self::assertSame($userSetId, $items[1]->getUserSet()->getId());
        self::assertSame($localSetId, $items[1]->getUserSet()->getLocalSet()->getId());
    }
}

