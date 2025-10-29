<?php

namespace App\Tests\CollectionManagement\Infrastructure\Service;

use Symfony\Contracts\Cache\CacheInterface;

/**
 * @author W. Zwertvaegher
 * To test that RebrickableCacheManager may call the clear method only it its CacheInterface
 * is an instance of AbstractAdapter, we need a mock CacheInterface which has a clear() method.
 * This allows to mock it and check that the clear() method is never called
 */
class MockCacheInterfaceImplementation implements CacheInterface
{

    /**
     * @param string $key
     * @param callable $callback
     * @param float|null $beta
     * @param array|null $metadata
     * @return null
     */
    public function get(string $key, callable $callback, ?float $beta = null, ?array &$metadata = null): null
    {
        return null;
    }

    public function delete(string $key): bool
    {
        return true;
    }

    public function clear(): bool
    {
        return true;
    }
}
