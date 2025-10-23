<?php

namespace App\CollectionManagement\Infrastructure\Service;

use App\CollectionManagement\Domain\Model\External\ExternalElementCollection;
use App\CollectionManagement\Domain\Model\External\ExternalSetElementCollection;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Model\SetCollection;
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

    public function getSets(string $search, callable $callback): ?SetCollection
    {
        // return cache when present
        return $this->cache->get('search_set_'.md5(strtolower($search)), function (ItemInterface $item) use ($search, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($search);
        });
    }

    public function getParts(string $search, callable $callback): ?PartCollection
    {
        // return cache when present
        return $this->cache->get('search_part_'.md5(strtolower($search)), function (ItemInterface $item) use ($search, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($search);
        });
    }

    public function getPartElements(string $partExternalId, callable $callback): ?ExternalElementCollection
    {
        // return cache when present
        return $this->cache->get('get_part_elements'.md5(strtolower($partExternalId)), function (ItemInterface $item) use ($partExternalId, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($partExternalId);
        });
    }

    public function getSetElements(string $setExternalId, callable $callback): ?ExternalSetElementCollection
    {
        // return cache when present
        return $this->cache->get('get_set_elements'.md5(strtolower($setExternalId)), function (ItemInterface $item) use ($setExternalId, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($setExternalId);
        });
    }

    public function clear(): void
    {
        if($this->cache instanceof AbstractAdapter){
            $this->cache->clear();
        }
    }
}
