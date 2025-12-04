<?php

namespace App\Tests\CollectionManagement\Unit\Domain\Application\Handler;

use App\CollectionManagement\Application\Command\SearchPartQuery;
use App\CollectionManagement\Application\Handler\SearchPartHandler;
use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Service\PartService;
use App\Shared\Domain\Model\EntityId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class SearchPartHandlerTest extends TestCase
{
    #[Test]
    public function invokeDelegatesToPartServiceWithExpectedArguments(): void
    {
        $userId = EntityId::fromString('abcd1234-abcd-4bcd-abcd-abcd1234abcd');
        $query = new SearchPartQuery('brick', $userId);

        $expectedResult = new PartCollection([
            new ExternalPart('externalId1', 'legoId1', 'Part 1', 'image1'),
            new ExternalPart('externalId2', 'legoId2', 'Part 2', 'image2'),
        ]);

        $partService = $this->createMock(PartService::class);
        $partService
            ->expects(self::once())
            ->method('findParts')
            ->with('brick', $userId)
            ->willReturn($expectedResult);

        $handler = new SearchPartHandler($partService);

        $result = $handler($query);

        self::assertSame($expectedResult, $result);
    }

    #[Test]
    public function invokeHandlesQueryWithoutUserId(): void
    {
        $query = new SearchPartQuery('plate');
        $expectedResult = new PartCollection([
            new ExternalPart('externalId1', 'legoId1', 'Part 1', 'image1'),
            new ExternalPart('externalId2', 'legoId2', 'Part 2', 'image2'),
        ]);

        $partService = $this->createMock(PartService::class);
        $partService
            ->expects(self::once())
            ->method('findParts')
            ->with('plate', null)
            ->willReturn($expectedResult);

        $handler = new SearchPartHandler($partService);

        $result = $handler($query);

        self::assertSame($expectedResult, $result);
    }
}
