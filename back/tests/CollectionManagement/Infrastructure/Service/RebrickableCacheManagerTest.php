<?php

namespace App\Tests\CollectionManagement\Infrastructure\Service;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RebrickableCacheManagerTest extends TestCase
{

    #[Test]
    public function testCacheManager()
    {
        // TODO
        /*
        $search = 'Star Wars';

        $cacheItem = $this->createMock(ItemInterface::class);
        $cacheItem->expects($this->once())
            ->method('expiresAfter')
            ->with(86400);

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->with($cacheKey, $this->callback(function ($callback) use ($cacheItem) {
                // On exécute le callback en simulant le cache miss
                $result = $callback($cacheItem);
                $this->assertIsArray($result);
                return true;
            }))
            ->willReturn(['set1', 'set2']);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('toArray')
            ->willReturn(['set1', 'set2']);

        $expectedOptions = [
            'headers' => [
                'Authorization' => 'Authorization: key FAKE_API_KEY',
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

        $loader = new RebrickableDataLoader($cache, $httpClient, 'FAKE_API_KEY');

        $sets = $loader->findSets($search);

        $this->assertSame(['set1', 'set2'], $sets);*/
    }
}
