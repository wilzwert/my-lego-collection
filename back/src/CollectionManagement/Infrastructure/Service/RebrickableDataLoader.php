<?php

namespace App\CollectionManagement\Infrastructure\Service;

use App\CollectionManagement\Domain\Model\ExternalSet;
use App\CollectionManagement\Domain\Service\LegoDataLoader;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author W. Zwertvaegher
 * Lego data loader ; this will be our only loader at the moment.
 * This loader uses a cache to avoid external requests when possible
 *
 */

#[Autoconfigure]
class RebrickableDataLoader implements LegoDataLoader
{
    private const API_BASE_URL = 'https://rebrickable.com/api/v3/lego/';

    public function __construct(
        private readonly RebrickableCacheManager $cacheManager,
        private readonly HttpClientInterface $httpClient,
        private string $apiKey
    )
    {}

    private function fetchFromExternalApi($endpointUri, $search): array {
        echo "Fetching from external API\n";
        $response = $this->httpClient->request(
            'GET',
            sprintf('%s%s?search=%s', self::API_BASE_URL, $endpointUri, $search),
            array(
                'headers' => [
                    'Authorization' => sprintf('key %s', $this->apiKey),
                ]
            )
        );

        // convert rebrickable data to an array of ExternalSet
        return array_map(
          fn($item) => new ExternalSet(
              preg_replace('/-.*$/', '', $item['set_num']),
              $item['set_num'],
              $item['name'],
              $item['num_parts'],
              $item['set_img_url'] ?? '',
              $item['year']
          ),
            $response->toArray()['results']
        );
    }

    private function fetchSetsFromExternalApi(string $search): array {
        echo "fetching SETS from external API\n";
        return $this->fetchFromExternalApi('sets/', $search);
    }

    #[\Override]
    public function findSets(string $search): array
    {
        return $this->cacheManager->getSets($search, fn($s) => $this->fetchSetsFromExternalApi($s));
    }

    #[\Override]
    public function findParts(string $search): array
    {
        // return cache when present

        // fetch from rebrickable api and then cache results when needed
        return [];
    }
}
