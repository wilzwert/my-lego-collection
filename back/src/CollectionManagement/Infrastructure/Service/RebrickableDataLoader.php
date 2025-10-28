<?php

namespace App\CollectionManagement\Infrastructure\Service;

use App\CollectionManagement\Domain\Model\External\ExternalElement;
use App\CollectionManagement\Domain\Model\External\ExternalElementCollection;
use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\External\ExternalSetElement;
use App\CollectionManagement\Domain\Model\External\ExternalSetElementCollection;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Domain\Service\LegoDataLoader;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Wilhelm Zwertvaegher
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

    /**
     * @param string $endpointUri
     * @return array<mixed>|null
     *
     * */
    private function fetchFromExternalApi($endpointUri): ?array
    {
        try {
            $response = $this->httpClient->request(
                'GET',
                sprintf('%s%s', self::API_BASE_URL, $endpointUri),
                array(
                    'headers' => [
                        'Authorization' => sprintf('key %s', $this->apiKey),
                    ]
                )
            );
            // return raw data fetched from external API
            $data = $response->toArray();
            return $data['results'] ?? [];
        }
        // TODO : properly handle this throwable
        catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param string $search
     * @return SetCollection|null
     */
    private function fetchSetsFromExternalApi(string $search): ?SetCollection {
        $results = $this->fetchFromExternalApi(sprintf('sets/?search=%s', $search));
        if($results === null){
            return null;
        }

        return new SetCollection(
            array_map(
                fn($item) => new ExternalSet(
                    $item['set_num'],
                    preg_replace('/-.*$/', '', $item['set_num']),
                    $item['name'],
                    $item['num_parts'],
                    $item['set_img_url'] ?? '',
                    $item['year']
                ),
                $results
            )
        );
    }

    /**
     * @param string $search
     * @return PartCollection|null
     */
    private function fetchPartsFromExternalApi(string $search): ?PartCollection {
        $results = $this->fetchFromExternalApi(sprintf('parts/?search=%s', $search));
        if($results === null){
            return null;
        }

        return new PartCollection(
            array_map(
                fn($item) => new ExternalPart(
                    $item['part_num'],
                    isset($item['external_ids']['LEGO']) ? $item['external_ids']['LEGO'][0] : '',
                    $item['name'],
                    $item['part_img_url'] ?? ''
                ),
                $results
            )
        );
    }

    /**
     * @param string $partExternalId
     * @return ExternalElementCollection|null
     */
    private function fetchPartElementsFromExternalApi(string $partExternalId): ?ExternalElementCollection {
        $results = $this->fetchFromExternalApi(sprintf('parts/%s/colors/', $partExternalId));
        if($results === null){
            return null;
        }

        return new ExternalElementCollection(
            array_map(
                fn($item) => new ExternalElement(
                    $item['elements'][0],
                    $item['elements'][0],
                    $partExternalId,
                    $item['part_img_url'] ?? '',
                    $item['color_id'],
                    $item['color_name']
                ),
                $results
            )
        );
    }

    /**
     * @param string $setExternalId
     * @return ExternalSetElementCollection|null
     */
    private function fetchSetElementsFromExternalApi(string $setExternalId): ?ExternalSetElementCollection {
        $results = $this->fetchFromExternalApi(sprintf('sets/%s/parts/?inc_part_details=1', $setExternalId));
        if($results === null){
            return null;
        }

        return new ExternalSetElementCollection(
            array_map(
                fn($item) => new ExternalSetElement(
                    $item['element_id'],
                    $setExternalId,
                    $item['part']['part_num'],
                    $item['quantity']
                ),
                array_filter(
                    $results,
                    fn($item) => $item['is_spare'] === false
                )
            )
        );
    }

    #[\Override]
    public function findSets(string $search): ?SetCollection
    {
        return $this->cacheManager->getSets($search, fn($s) => $this->fetchSetsFromExternalApi($s));
    }

    #[\Override]
    public function findParts(string $search): ?PartCollection
    {
        return $this->cacheManager->getParts($search, fn($s) => $this->fetchPartsFromExternalApi($s));
    }

    #[Override]
    public function getSetParts(string $setExternalId): ?ExternalSetElementCollection
    {
        return new ExternalSetElementCollection([]);
    }

    #[Override]
    public function getPartElements(string $partExternalId): ?ExternalElementCollection
    {
        return $this->cacheManager->getPartElements($partExternalId, fn($s) => $this->fetchPartElementsFromExternalApi($partExternalId));
    }

    #[Override]
    public function getSetElements(string $setExternalId): ?ExternalSetElementCollection
    {
        return $this->cacheManager->getSetElements($setExternalId, fn($s) => $this->fetchSetElementsFromExternalApi($setExternalId));
    }
}
