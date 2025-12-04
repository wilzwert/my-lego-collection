<?php

namespace App\Tests\CollectionManagement\Unit\Infrastructure\Service;

use App\CollectionManagement\Domain\Model\External\ExternalElement;
use App\CollectionManagement\Domain\Model\External\ExternalElementCollection;
use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\External\ExternalSetElement;
use App\CollectionManagement\Domain\Model\External\ExternalSetElementCollection;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Infrastructure\Service\ExternalDataCacheManager;
use App\CollectionManagement\Infrastructure\Service\RebrickableDataLoader;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[Group('Rebrickable')]
final class RebrickableDataLoaderTest extends TestCase
{

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

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->never())
        ->method('request');

        $loader = new RebrickableDataLoader($cacheManager, $httpClient, 'FAKE_API_KEY');

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

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(
                array(
                    'results' => [
                        ['set_num' => '1-1', 'name' => 'BaseSet 1', 'num_parts' => 10, 'year' => 2008],
                        ['set_num' => '2-1', 'name' => 'BaseSet 2', 'num_parts' => 20, 'year' => 2007]
                    ]
                )
            );

        $expectedOptions = [
            'headers' => [
                'Authorization' => 'key FAKE_API_KEY',
            ],
        ];
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->stringContains('sets/'),
                $expectedOptions
            )
            ->willReturn($response);

        $loader = new RebrickableDataLoader($cacheManager, $httpClient, 'FAKE_API_KEY');

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

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->never())
            ->method('request');

        $loader = new RebrickableDataLoader($cacheManager, $httpClient, 'FAKE_API_KEY');

        $parts = $loader->findParts($search);

        self::assertSame($expectedParts, $parts);
    }

    #[Test]
    public function shouldGetPartsWithHttpClient(): void
    {
        $search = 'part search';
        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $externalParts = new PartCollection(array(
            new ExternalPart('1-1', '1', 'Part 1', ''),
            new ExternalPart('2-1', '2', 'Part 2', '')
        ));
        $cacheManager->expects($this->once())
            ->method('getParts')
            ->with($search, $this->callback(function ($callback) use ($search, $externalParts) {
                // fake cache miss
                $result = $callback($search);
                self::assertInstanceOf(PartCollection::class, $result);
                self::assertCount(2, $result);
                self::assertEquals($externalParts, $result);
                return true;
            }))
            ->willReturn($externalParts);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(
                array(
                    'results' => [
                        ['part_num' => '1-1', 'name' => 'Part 1', 'part_img_url' => '', 'external_ids' => ['LEGO' => ['1']]],
                        ['part_num' => '2-1', 'name' => 'Part 2', 'part_img_url' => '', 'external_ids' => ['LEGO' => ['2']]],
                    ]
                )
            );

        $expectedOptions = [
            'headers' => [
                'Authorization' => 'key FAKE_API_KEY',
            ],
        ];
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->stringContains('parts/'),
                $expectedOptions
            )
            ->willReturn($response);

        $loader = new RebrickableDataLoader($cacheManager, $httpClient, 'FAKE_API_KEY');

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

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->never())
            ->method('request');

        $loader = new RebrickableDataLoader($cacheManager, $httpClient, 'FAKE_API_KEY');

        $elements = $loader->getPartElements($externalPartId);

        self::assertSame($expectedElements, $elements);
    }

    #[Test]
    public function shouldGetPartElementsWithHttpClient(): void
    {
        $externalPartId = '93061';
        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $externalElements = new ExternalElementCollection([
            new ExternalElement('legoId1', 'legoId1', '93061', '', '0', 'Black'),
            new ExternalElement('legoId2', 'legoId2', '93061', '', '4', 'Red'),
        ]);
        $cacheManager->expects($this->once())
            ->method('getPartElements')
            ->with($externalPartId, $this->callback(function ($callback) use ($externalPartId, $externalElements) {
                // fake cache miss
                $result = $callback($externalPartId);
                self::assertInstanceOf(ExternalElementCollection::class, $result);
                self::assertCount(2, $result);
                self::assertEquals($externalElements, $result);
                return true;
            }))
            ->willReturn($externalElements);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(
                array(
                    'results' => [
                        ["color_id" => 0, "color_name" => "Black", "num_sets" => 26, "num_set_parts" => 56, "part_img_url" => "", "elements" => ["legoId1"]],
                        ["color_id" => 4, "color_name" => "Red", "num_sets" => 1, "num_set_parts" => 2, "part_img_url" => "", "elements" =>["legoId2"]]
                    ]
                )
            );

        $expectedOptions = [
            'headers' => [
                'Authorization' => 'key FAKE_API_KEY',
            ],
        ];
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->stringContains('parts/'),
                $expectedOptions
            )
            ->willReturn($response);

        $loader = new RebrickableDataLoader($cacheManager, $httpClient, 'FAKE_API_KEY');

        $elements = $loader->getPartElements($externalPartId);
        self::assertSame($externalElements, $elements);
    }

    #[Test]
    public function shouldGetSetElementsFromCache(): void
    {
        $externalSetId = '93061';

        $expectedElements = new ExternalSetElementCollection([
            new ExternalSetElement('externalId1', '93061', 'externalPartId1', 5),
            new ExternalSetElement('externalId2', '93061', 'externalPartId2', 10),
        ]);

        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $cacheManager->expects($this->once())
            ->method('getSetElements')
            ->with($externalSetId, $this->anything())
            ->willReturn($expectedElements);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->never())
            ->method('request');

        $loader = new RebrickableDataLoader($cacheManager, $httpClient, 'FAKE_API_KEY');

        $elements = $loader->getSetElements($externalSetId);

        self::assertSame($expectedElements, $elements);
    }

    #[Test]
    public function shouldGetSetElementsWithHttpClient(): void
    {
        $externalSetId = '93061';
        $cacheManager = $this->createMock(ExternalDataCacheManager::class);
        $externalElements = new ExternalSetElementCollection([
            new ExternalSetElement('externalId1', '93061', 'externalPartId1', 5),
            new ExternalSetElement('externalId2', '93061', 'externalPartId2', 10),
        ]);
        $cacheManager->expects($this->once())
            ->method('getSetElements')
            ->with($externalSetId, $this->callback(function ($callback) use ($externalSetId, $externalElements) {
                // fake cache miss
                $result = $callback($externalSetId);
                self::assertInstanceOf(ExternalSetElementCollection::class, $result);
                self::assertCount(2, $result);
                self::assertEquals($externalElements, $result);
                return true;
            }))
            ->willReturn($externalElements);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(
                array(
                    'results' => [
                        ["part" => ["part_num" => "externalPartId1"], "quantity" => 5, "is_spare" => false, "element_id" => "externalId1"],
                        ["part" => ["part_num" => "externalPartId2"], "quantity" => 10, "is_spare" => false, "element_id" => "externalId2"],
                        ["part" => ["part_num" => "externalPartId2"], "quantity" => 2, "is_spare" => true, "element_id" => "externalId2"],
                    ]
                )
            );

        $expectedOptions = [
            'headers' => [
                'Authorization' => 'key FAKE_API_KEY',
            ],
        ];
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->stringContains('parts/'),
                $expectedOptions
            )
            ->willReturn($response);

        $loader = new RebrickableDataLoader($cacheManager, $httpClient, 'FAKE_API_KEY');

        $elements = $loader->getSetElements($externalSetId);
        self::assertSame($externalElements, $elements);
    }
}
