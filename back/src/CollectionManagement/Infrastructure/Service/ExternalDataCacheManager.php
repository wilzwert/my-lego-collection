<?php

namespace App\CollectionManagement\Infrastructure\Service;

use App\CollectionManagement\Domain\Model\External\ExternalColor;
use App\CollectionManagement\Domain\Model\External\ExternalElement;
use App\CollectionManagement\Domain\Model\External\ExternalElementCollection;
use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
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

    public function getPart(string $partNum, callable $callback): ExternalPart
    {
        return $this->cache->get('get_part_'.$this->hash($partNum), function (ItemInterface $item) use ($partNum, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($partNum);
        });
    }

    public function getColor(string $colorId, callable $callback): ExternalColor
    {
        return $this->cache->get('get_color_'.$this->hash($colorId), function (ItemInterface $item) use ($colorId, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($colorId);
        });
    }

    public function getElement(string $elementId, callable $callback): ExternalElement
    {
        return $this->cache->get('get_element_'.$this->hash($elementId), function (ItemInterface $item) use ($elementId, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($elementId);
        });
    }

    public function getSets(string $search, callable $callback): ?SetCollection
    {
        // return cache when present
        // TODO it could be interesting to find a way to cache found Sets in case we want to retrieve them individually later in getSet
        return $this->cache->get('search_set_'.$this->hash($search), function (ItemInterface $item) use ($search, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($search);
        });
    }

    public function getSet(string $externalSetId, callable $callback): ?ExternalSet
    {
        // return cache when present
        return $this->cache->get('get_set_' . $this->hash($externalSetId), function (ItemInterface $item) use ($externalSetId, $callback) {
            $item->expiresAfter(self::TTL);
            return $callback($externalSetId);
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
