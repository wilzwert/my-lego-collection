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
class ExternalDataCacheManager
{
    private const int TTL = 86400;

    public function __construct(
        private readonly CacheInterface $cache,
    ) {
    }

    private function hash(string $key): string
    {
        return hash('sha256', strtolower($key));
    }

    public function getSets(string $search, callable $callback): ?SetCollection
    {
        // return cache when present
        return $this->cache->get('search_set_'.$this->hash($search), function (ItemInterface $item) use ($search, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($search);
        });
    }

    public function getParts(string $search, callable $callback): ?PartCollection
    {
        // return cache when present
        return $this->cache->get('search_part_'.$this->hash($search), function (ItemInterface $item) use ($search, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($search);
        });
    }

    public function getPartElements(string $partExternalId, callable $callback): ?ExternalElementCollection
    {
        // return cache when present
        return $this->cache->get('get_part_elements'.$this->hash($partExternalId), function (ItemInterface $item) use ($partExternalId, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($partExternalId);
        });
    }

    public function getSetElements(string $setExternalId, callable $callback): ?ExternalSetElementCollection
    {
        // return cache when present
        return $this->cache->get('get_set_elements'.$this->hash($setExternalId), function (ItemInterface $item) use ($setExternalId, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($setExternalId);
        });
    }

    public function clear(): void
    {
        if ($this->cache instanceof AbstractAdapter) {
            $this->cache->clear();
        }
    }
}
