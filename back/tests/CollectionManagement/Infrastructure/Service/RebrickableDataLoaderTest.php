<?php

namespace App\Tests\CollectionManagement\Infrastructure\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use PHPUnit\Framework\Attributes\Test;
use App\CollectionManagement\Infrastructure\Service\RebrickableDataLoader;
use App\CollectionManagement\Infrastructure\Service\RebrickableCacheManager;
use App\CollectionManagement\Domain\Model\ExternalSet;
final class RebrickableDataLoaderTest extends TestCase
{

    #[Test]
    public function shouldGetSetsFromCache(): void
    {
        $search = 'Star Wars';

        $cacheManager = $this->createMock(RebrickableCacheManager::class);
        $cacheManager->expects($this->once())
            ->method('getSets')
            ->with($search, $this->anything())
            ->willReturn(['cached_set1', 'cached_set2']);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->never())
        ->method('request');

        $loader = new RebrickableDataLoader($cacheManager, $httpClient, 'FAKE_API_KEY');

        $sets = $loader->findSets($search);

        $this->assertSame(['cached_set1', 'cached_set2'], $sets);
    }

    #[Test]
    public function shouldGetSetsWithHttpClient(): void
    {
        $search = 'Star Wars';
        $cacheManager = $this->createMock(RebrickableCacheManager::class);
        $externalSets = array(
          new ExternalSet('1', '1-1', 'Set 1', 10, '', 2008),
          new ExternalSet('2', '2-1', 'Set 2', 20, '', 2007)
        );
        $cacheManager->expects($this->once())
            ->method('getSets')
            ->with($search, $this->callback(function ($callback) use ($search, $externalSets) {
                // fake cache miss
                $result = $callback($search);
                $this->assertIsArray($result);
                $this->assertCount(2, $result);
                $this->assertEquals($externalSets, $result);
                return true;
            }))
            ->willReturn($externalSets);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(
                array(
                    'results' => [
                        ['set_num' => '1-1', 'name' => 'Set 1', 'num_parts' => 10, 'year' => 2008],
                        ['set_num' => '2-1', 'name' => 'Set 2', 'num_parts' => 20, 'year' => 2007]
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
        $this->assertSame($externalSets, $sets);
    }
}
