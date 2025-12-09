<?php

namespace App\Tests\CollectionManagement\Unit\Application\Handler;

use App\CollectionManagement\Application\Command\SearchSetQuery;
use App\CollectionManagement\Application\Handler\SearchSetHandler;
use App\CollectionManagement\Application\Service\FindSetsService;
use App\CollectionManagement\Domain\Model\EnrichedSet;
use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\CollectionManagement\Domain\Service\SetService;
use App\Shared\Domain\Model\EntityId;
use App\Tests\CollectionManagement\Utilities\CollectionManagementTestsUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class SearchSetHandlerTest extends TestCase
{
    #[Test]
    public function invokeDelegatesToPartServiceWithExpectedArguments(): void
    {
        $userId = EntityId::fromString('abcd1234-abcd-4bcd-abcd-abcd1234abcd');
        $query = new SearchSetQuery('set', $userId);

        $expectedResult = new EnrichedSetCollection([
            new EnrichedSet(new ExternalSet('externalId1', 'legoId1', 'Set 1', 100, 'image1', 2000)),
            new EnrichedSet(CollectionManagementTestsUtility::generateKnownSet())
        ]);

        $findSetsService = $this->createMock(FindSetsService::class);
        $findSetsService
            ->expects($this->once())
            ->method('findSets')
            ->with('set', $userId)
            ->willReturn($expectedResult);

        $handler = new SearchSetHandler($findSetsService);

        $result = $handler($query);

        self::assertSame($expectedResult, $result);
    }

    #[Test]
    public function invokeHandlesQueryWithoutUserId(): void
    {
        $query = new SearchSetQuery('set');

        // result contains external set (fetched from external source) as well a local set
        $expectedResult = new EnrichedSetCollection([
            new EnrichedSet(new ExternalSet('externalId1', 'legoId1', 'Set 1', 100, 'image1', 2000)),
            new EnrichedSet(CollectionManagementTestsUtility::generateKnownSet())
        ]);

        $findSetsService = $this->createMock(FindSetsService::class);
        $findSetsService
            ->expects($this->once())
            ->method('findSets')
            ->with('set', null)
            ->willReturn($expectedResult);

        $handler = new SearchSetHandler($findSetsService);

        $result = $handler($query);

        self::assertSame($expectedResult, $result);
    }
}
