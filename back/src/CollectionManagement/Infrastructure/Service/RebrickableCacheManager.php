<?php

namespace App\CollectionManagement\Infrastructure\Service;

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Autoconfigure]
class RebrickableCacheManager
{

    private const TTL = 86400;

    public function __construct(
        private readonly CacheInterface $cache,
    )
    {}

    public function getSets(string $search, callable $callback): array
    {
        // return cache when present
        return $this->cache->get('search_set_'.md5(strtolower($search)), function (ItemInterface $item) use ($search, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($search);
        });
    }

    public function clear(): void
    {
        if($this->cache instanceof AbstractAdapter){
            $this->cache->clear();
        }
    }
}
