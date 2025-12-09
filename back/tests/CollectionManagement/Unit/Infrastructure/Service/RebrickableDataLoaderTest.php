<?php

namespace App\Tests\CollectionManagement\Unit\Infrastructure\Service;

use App\CollectionManagement\Domain\Model\External\ExternalColor;
use App\CollectionManagement\Domain\Model\External\ExternalElement;
use App\CollectionManagement\Domain\Model\External\ExternalElementCollection;
use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\External\ExternalSetElement;
use App\CollectionManagement\Domain\Model\External\ExternalSetElementCollection;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Infrastructure\Service\ExternalDataCacheManager;
use App\CollectionManagement\Infrastructure\Service\RebrickableDataFetcher;
use App\CollectionManagement\Infrastructure\Service\RebrickableDataLoader;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('Rebrickable')]
final class RebrickableDataLoaderTest extends TestCase
{
    #[Test]
    public function shouldGetSetFromCache(): void
    {
        $externalSetId = '75353-1';
        $expectedSet = new ExternalSet('externalId1', 'legoId1', 'Cached set 1', 100, '', 2005);

        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $cacheManager->expects($this->once())
            ->method('getSet')
            ->with($externalSetId, $this->anything())
            ->willReturn($expectedSet);

        $fetcher = $this->createMock(RebrickableDataFetcher::class);
        $fetcher->expects($this->never())
            ->method('fetchFromApi');

        $loader = new RebrickableDataLoader($cacheManager, $fetcher);

        $set = $loader->getSet($externalSetId);

        self::assertSame($expectedSet, $set);
    }

    #[Test]
    public function shouldGetSetWithHttpClient(): void
    {
        $externalSetId = '75353-1';
        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $externalSet = new ExternalSet('1-1', '1', 'BaseSet 1', 10, '', 2008);
        $cacheManager->expects($this->once())
            ->method('getSet')
            ->with($externalSetId, $this->callback(function ($callback) use ($externalSetId, $externalSet) {
                // fake cache miss
                $result = $callback($externalSetId);
                self::assertInstanceOf(ExternalSet::class, $result);
                self::assertEquals($externalSet, $result);
                return true;
            }))
            ->willReturn($externalSet);

        $fetcher = $this->createMock(RebrickableDataFetcher::class);
        $fetcher->expects($this->once())
            ->method('fetchFromApi')
            ->willreturn(['set_num' => '1-1', 'name' => 'BaseSet 1', 'num_parts' => 10, 'year' => 2008]);

        $loader = new RebrickableDataLoader($cacheManager, $fetcher);

        $set = $loader->getSet($externalSetId);
        self::assertSame($externalSet, $set);

    }

    #[Test]
    public function shouldGetSetsFromCache(): void
    {
        $search = 'Star Wars';

        $expectedSets = new SetCollection([
            new ExternalSet('externalId1', 'legoId1', 'Cached set 1', 100, '', 2005),
            new ExternalSet('externalId2', 'legoId2', 'Cached set 2', 200, '', 2006),
        ]);

        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $cacheManager->expects($this->once())
            ->method('getSets')
            ->with($search, $this->anything())
            ->willReturn($expectedSets);

        $fetcher = $this->createMock(RebrickableDataFetcher::class);
        $fetcher->expects($this->never())
            ->method('fetchFromApi');

        $loader = new RebrickableDataLoader($cacheManager, $fetcher);

        $sets = $loader->findSets($search);

        self::assertSame($expectedSets, $sets);
    }

    #[Test]
    public function shouldGetSetsWithHttpClient(): void
    {
        $search = 'Star Wars';
        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $externalSets = new SetCollection(array(
          new ExternalSet('1-1', '1', 'BaseSet 1', 10, '', 2008),
          new ExternalSet('2-1', '2', 'BaseSet 2', 20, '', 2007)
        ));
        $cacheManager->expects($this->once())
            ->method('getSets')
            ->with($search, $this->callback(function ($callback) use ($search, $externalSets) {
                // fake cache miss
                $result = $callback($search);
                self::assertInstanceOf(SetCollection::class, $result);
                self::assertCount(2, $result);
                self::assertEquals($externalSets, $result);
                return true;
            }))
            ->willReturn($externalSets);

        $fetcher = $this->createMock(RebrickableDataFetcher::class);
        $fetcher->expects($this->once())
            ->method('fetchFromApi')
            ->willReturn(array(
                'results' => [
                    ['set_num' => '1-1', 'name' => 'BaseSet 1', 'num_parts' => 10, 'year' => 2008],
                    ['set_num' => '2-1', 'name' => 'BaseSet 2', 'num_parts' => 20, 'year' => 2007]
                ]
            ));

        $loader = new RebrickableDataLoader($cacheManager, $fetcher);

        $sets = $loader->findSets($search);
        self::assertSame($externalSets, $sets);
    }


    #[Test]
    public function shouldGetPartsFromCache(): void
    {
        $search = 'part search';

        $expectedParts = new PartCollection([
            new ExternalPart('externalId1', 'legoId1', 'Cached part 1', ''),
            new ExternalPart('externalId2', 'legoId2', 'Cached part 2', ''),
        ]);

        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $cacheManager->expects($this->once())
            ->method('getParts')
            ->with($search, $this->anything())
            ->willReturn($expectedParts);

        $fetcher = $this->createMock(RebrickableDataFetcher::class);
        $fetcher->expects($this->never())
            ->method('fetchFromApi');

        $loader = new RebrickableDataLoader($cacheManager, $fetcher);

        $parts = $loader->findParts($search);

        self::assertSame($expectedParts, $parts);
    }

    #[Test]
    public function shouldGetPartsWithHttpClient(): void
    {
        $search = 'part search';
        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $externalPart1 = new ExternalPart('1-1', '1', 'Part 1', '');
        $externalPart2 = new ExternalPart('2-1', '2', 'Part 2', '');
        $externalParts = new PartCollection(array($externalPart1, $externalPart2));

        $cacheManager->expects($this->once())
            ->method('getParts')
            ->with($search, $this->callback(function ($callback) use ($search, $externalParts) {
                // fake cache miss by calling the callback explicitly
                $result = $callback($search);
                self::assertInstanceOf(PartCollection::class, $result);
                self::assertCount(2, $result);
                self::assertEquals($externalParts, $result);
                return true;
            }))
            ->willReturn($externalParts);

        $externalPartIds = [];
        $cacheManager->expects($this->exactly(2))
            ->method('getPart')
            ->with(
                $this->callback(function ($externalId) use (&$externalPartIds) {
                    $externalPartIds[] = $externalId;
                    return true;
                }),
                $this->callback(function ($callback) {
                    // fake cache miss by calling the callback explicitly
                    $result = $callback();
                    self::assertInstanceOf(ExternalPart::class, $result);
                    return true;
                })
            )
            ->willReturnOnConsecutiveCalls($externalPart1, $externalPart2);

        $fetcher = $this->createMock(RebrickableDataFetcher::class);
        $fetcher->expects($this->once())
            ->method('fetchFromApi')
            ->willReturn(array(
                'results' => [
                    ['part_num' => '1-1', 'name' => 'Part 1', 'part_img_url' => '', 'external_ids' => ['LEGO' => ['1']]],
                    ['part_num' => '2-1', 'name' => 'Part 2', 'part_img_url' => '', 'external_ids' => ['LEGO' => ['2']]],
                ]
            ));

        $loader = new RebrickableDataLoader($cacheManager, $fetcher);

        $parts = $loader->findParts($search);
        self::assertSame($externalParts, $parts);
    }

    #[Test]
    public function shouldGetPartElementsFromCache(): void
    {
        $externalPartId = '93061';

        $expectedElements = new ExternalElementCollection([
            new ExternalElement('externalId1', 'legoId1', 'externalPartId1', '', '0', 'Black'),
            new ExternalElement('externalId2', 'legoId2', 'externalPartId2', '', '4', 'Red'),
        ]);

        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $cacheManager->expects($this->once())
            ->method('getPartElements')
            ->with($externalPartId, $this->anything())
            ->willReturn($expectedElements);

        $fetcher = $this->createMock(RebrickableDataFetcher::class);
        $fetcher->expects($this->never())
            ->method('fetchFromApi');

        $loader = new RebrickableDataLoader($cacheManager, $fetcher);

        $elements = $loader->getPartElements($externalPartId);

        self::assertSame($expectedElements, $elements);
    }

    #[Test]
    public function shouldGetPartElementsWithHttpClient(): void
    {
        $externalPartId = '93061';
        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $externalElement1 = new ExternalElement('legoId1', 'legoId1', '93061', '', '0');
        $externalElement2 = new ExternalElement('legoId2', 'legoId2', '93061', '', '4');
        $externalElements = new ExternalElementCollection([$externalElement1, $externalElement2]);
        $cacheManager->expects($this->once())
            ->method('getPartElements')
            ->with($externalPartId, $this->callback(function ($callback) use ($externalPartId, $externalElements) {
                // fake cache miss by calling the callback explicitly
                $result = $callback($externalPartId);
                self::assertInstanceOf(ExternalElementCollection::class, $result);
                self::assertCount(2, $result);
                self::assertEquals($externalElements, $result);
                return true;
            }))
            ->willReturn($externalElements);

        $externalElementIds = [];
        $cacheManager->expects($this->exactly(2))
            ->method('getElement')
            ->with(
                $this->callback(function ($externalId) use (&$externalElementIds) {
                    $externalElementIds[] = $externalId;
                    return true;
                }),
                $this->callback(function ($callback) {
                    // fake cache miss by calling the callback explicitly
                    $result = $callback();
                    self::assertInstanceOf(ExternalElement::class, $result);
                    return true;
                })
            )
            ->willReturnOnConsecutiveCalls($externalElement1, $externalElement2);

        $fetcher = $this->createMock(RebrickableDataFetcher::class);
        $fetcher->expects($this->once())
            ->method('fetchFromApi')
            ->willReturn(array(
                'results' => [
                    ["color_id" => 0, "color_name" => "Black", "num_sets" => 26, "num_set_parts" => 56, "part_img_url" => "", "elements" => ["legoId1"]],
                    ["color_id" => 4, "color_name" => "Red", "num_sets" => 1, "num_set_parts" => 2, "part_img_url" => "", "elements" =>["legoId2"]]
                ]
            ));

        $loader = new RebrickableDataLoader($cacheManager, $fetcher);

        $elements = $loader->getPartElements($externalPartId);
        self::assertSame($externalElements, $elements);
        self::assertEquals([$externalElement1->getExternalId(), $externalElement2->getExternalId()], $externalElementIds);
    }

    #[Test]
    public function shouldGetSetElementsFromCache(): void
    {
        $externalSetId = '93061';
        $expectedElements = new ExternalSetElementCollection([
            $this->createStub(ExternalSetElement::class),
            $this->createStub(ExternalSetElement::class)
        ]);

        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $cacheManager->expects($this->once())
            ->method('getSetElements')
            ->with($externalSetId, $this->anything())
            ->willReturn($expectedElements);

        $fetcher = $this->createMock(RebrickableDataFetcher::class);
        $fetcher->expects($this->never())
            ->method('fetchFromApi');

        $loader = new RebrickableDataLoader($cacheManager, $fetcher);

        $elements = $loader->getSetElements($externalSetId);

        self::assertSame($expectedElements, $elements);
    }

    // FIXME : it seems a bit over complicated here
    #[Test]
    public function shouldGetSetElementsWithHttpClient(): void
    {
        $externalSetId = '93061';
        $cacheManager = $this->createMock(ExternalDataCacheManager::class);

        // we cannot stub ExternalSetElement because the cache manager callback actually uses it to build
        // the ExternalSetElement's ExternalElement
        $externalSetElement1 = new ExternalSetElement(
            $externalSetId,
            new ExternalElement('externalId1', 'externalId1', 'externalPartId1', '', '1'),
            new ExternalPart('externalPartId1', '', 'Part 1', ''),
            new ExternalColor('1', '', 'black', ''),
            10,
            0
        );

        $externalSetElement2 = new ExternalSetElement(
            $externalSetId,
            new ExternalElement('externalId2', 'externalId2', 'externalPartId2', '', '3'),
            new ExternalPart('externalPartId2', '', 'Part 2', ''),
            new ExternalColor('3', '', 'blue', ''),
            10,
            2
        );

        $externalSetElements = new ExternalSetElementCollection([$externalSetElement1, $externalSetElement2]);

        $cacheManager->expects($this->once())
            ->method('getSetElements')
            ->with($externalSetId, $this->callback(function ($callback) use ($externalSetId, $externalSetElements) {
                // fake cache miss
                $result = $callback($externalSetId);
                self::assertInstanceOf(ExternalSetElementCollection::class, $result);
                self::assertCount(2, $result);
                self::assertEquals($externalSetElements, $result);
                return true;
            }))
            ->willReturn($externalSetElements);

        $externalElementIds = [];
        $cacheManager->expects($this->exactly(2))
            ->method('getElement')
            ->with(
                $this->callback(function ($externalId) use (&$externalElementIds) {
                    $externalElementIds[] = $externalId;
                    return true;
                }),
                $this->callback(function ($callback) {
                    // fake cache miss by calling the callback explicitly
                    $result = $callback();
                    self::assertInstanceOf(ExternalElement::class, $result);
                    return true;
                })
            )
            ->willReturnOnConsecutiveCalls($externalSetElement1->getExternalElement(), $externalSetElement2->getExternalElement());

        $externalPartIds = [];
        $cacheManager->expects($this->exactly(2))
            ->method('getPart')
            ->with(
                $this->callback(function ($externalId) use (&$externalPartIds) {
                    $externalPartIds[] = $externalId;
                    return true;
                }),
                $this->callback(function ($callback) {
                    // fake cache miss by calling the callback explicitly
                    $result = $callback();
                    self::assertInstanceOf(ExternalPart::class, $result);
                    return true;
                })
            )
            ->willReturnOnConsecutiveCalls($externalSetElement1->getExternalPart(), $externalSetElement2->getExternalPart());

        $externalColorIds = [];
        $cacheManager->expects($this->exactly(2))
            ->method('getColor')
            ->with(
                $this->callback(function ($externalId) use (&$externalColorIds) {
                    $externalColorIds[] = $externalId;
                    return true;
                }),
                $this->callback(function ($callback) {
                    // fake cache miss by calling the callback explicitly
                    $result = $callback();
                    self::assertInstanceOf(ExternalColor::class, $result);
                    return true;
                })
            )
            ->willReturnOnConsecutiveCalls($externalSetElement1->getExternalColor(), $externalSetElement2->getExternalColor());


        $fetcher = $this->createMock(RebrickableDataFetcher::class);
        $fetcher->expects($this->once())
            ->method('fetchFromApi')
            ->willReturn(
                array(
                    'results' => [
                        ["part" => ["part_num" => "externalPartId1", 'name' => 'Part 1'], "color" => ["id" => 1, 'name' => 'black'], "quantity" => 10, "is_spare" => false, "element_id" => "externalId1"],
                        ["part" => ["part_num" => "externalPartId2", 'name' => 'Part 2'], "color" => ["id" => 3, 'name' => 'blue'], "quantity" => 10, "is_spare" => false, "element_id" => "externalId2"],
                        ["part" => ["part_num" => "externalPartId2", 'name' => 'Part 2'], "color" => ["id" => 3, 'name' => 'blue'], "quantity" => 2, "is_spare" => true, "element_id" => "externalId2"],
                    ]
                )
            );



        $loader = new RebrickableDataLoader($cacheManager, $fetcher);

        $elements = $loader->getSetElements($externalSetId);
        self::assertSame($externalSetElements, $elements);
        self::assertEquals(['externalId1', 'externalId2'], $externalElementIds);
        self::assertEquals(['externalPartId1', 'externalPartId2'], $externalPartIds);
        self::assertEquals(['1', '3'], $externalColorIds);
    }
}
