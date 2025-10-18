<?php

namespace App\CollectionManagement\Infrastructure\Service;

use App\CollectionManagement\Domain\Service\LegoDataLoader;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @author W. Zwertvaegher
 * Lego data loader ; this will be our only loader at the moment.
 * This loader uses a cache to avoid external requests when possible
 *
 */

#[Autoconfigure]
class RebrickableDataLoader implements LegoDataLoader
{

    public function __construct(private CacheInterface $cache) {}

    #[\Override]
    public function findSets(string $search): array
    {
        // return cache when present

        // fetch from rebrickable api and then cache results when needed
        return [];
    }

    #[\Override]
    public function findParts(string $search): array
    {
        // return cache when present

        // fetch from rebrickable api and then cache results when needed
        return [];
    }
}
