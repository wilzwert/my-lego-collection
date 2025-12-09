<?php

namespace App\CollectionManagement\Infrastructure\Service;

use App\CollectionManagement\Domain\Model\External\ExternalColor;
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

/**
 * @author Wilhelm Zwertvaegher
 * Lego data loader ; this will be our only loader at the moment.
 * This loader uses a cache to avoid external requests when possible
 *
 */

#[Autoconfigure]
class RebrickableDataLoader implements LegoDataLoader
{

    public function __construct(
        private readonly ExternalDataCacheManager $cacheManager,
        private readonly RebrickableDataFetcher $fetcher
    ) {
    }

    private function loadExternalPart(array $item): ExternalPart
    {
        return $this->cacheManager->getPart(
            $item['part_num'],
            fn () => new ExternalPart(
                $item['part_num'],
                isset($item['external_ids']['LEGO']) ? $item['external_ids']['LEGO'][0] : '',
                $item['name'],
                $item['part_img_url'] ?? ''
            )
        );
    }

    private function loadExternalColor(array $item): ExternalColor
    {
        return $this->cacheManager->getColor(
            $item['id'],
            fn () => new ExternalColor(
                $item['id'],
                isset($item['external_ids']['LEGO']) ? $item['external_ids']['LEGO']['ext_ids'][0] : '',
                $item['name'],
                $item['rgb'] ?? ''
            )
        );
    }

    private function loadExternalElement(string $elementId, string $partExternalId, string $elementImagePath, string $externalColorId): ExternalElement
    {
        return $this->cacheManager->getElement(
            $elementId,
            fn () => new ExternalElement(
                $elementId,
                $elementId,
                $partExternalId,
                $elementImagePath,
                $externalColorId
            )
        );
    }

    private function fetchSet(string $externalSetId): ?ExternalSet
    {
        $result = $this->fetcher->fetchFromApi(sprintf('sets/%s/', $externalSetId));
        if ($result === null || empty($result['set_num'])) {
            return null;
        }

        return new ExternalSet(
            $result['set_num'],
            preg_replace('/-.*$/', '', $result['set_num']),
            $result['name'],
            $result['num_parts'],
            $result['set_img_url'] ?? '',
            $result['year']
        );
    }

    /**
     * @param string $search
     * @return SetCollection|null
     */
    private function fetchSets(string $search): ?SetCollection
    {
        $results = $this->fetcher->fetchFromApi(sprintf('sets/?search=%s', $search));
        if ($results === null || empty($results['results'])) {
            return null;
        }

        return new SetCollection(
            array_map(
                fn ($item) => new ExternalSet(
                    $item['set_num'],
                    preg_replace('/-.*$/', '', $item['set_num']),
                    $item['name'],
                    $item['num_parts'],
                    $item['set_img_url'] ?? '',
                    $item['year']
                ),
                $results['results']
            )
        );
    }

    /**
     * @param string $search
     * @return PartCollection|null
     */
    private function fetchParts(string $search): ?PartCollection
    {
        $results = $this->fetcher->fetchFromApi(sprintf('parts/?search=%s', $search));
        if ($results === null) {
            return null;
        }

        return new PartCollection(
            array_map(
                fn ($item) => $this->loadExternalPart($item),
                $results['results']
            )
        );
    }

    /**
     * @param string $partExternalId
     * @return ExternalElementCollection|null
     */
    private function fetchPartElements(string $partExternalId): ?ExternalElementCollection
    {
        $results = $this->fetcher->fetchFromApi(sprintf('parts/%s/colors/', $partExternalId));
        if ($results === null) {
            return null;
        }

        return new ExternalElementCollection(
            array_map(
                fn ($item) => $this->loadExternalElement(
                    $item['elements'][0],
                    $partExternalId,
                    $item['part_img_url'] ?? '',
                    $item['color_id']
                ),
                $results['results']
            )
        );
    }

    /**
     * @param string $setExternalId
     * @return ExternalSetElementCollection|null
     */
    private function fetchSetElements(string $setExternalId): ?ExternalSetElementCollection
    {

        // first we get all the elements in a single array
        $allElements = [];
        $next = null;
        do {
            $page = '';
            if (!empty($next)) {
                preg_match('/page=([0-9+])/', $next, $matches);
                if (!empty($matches[1])) {
                    $page = 'page=' . $matches[1] .'&';
                }
            }
            $results = $this->fetcher->fetchFromApi(sprintf('sets/%s/parts/?%sinc_part_details=1', $setExternalId, $page));
            array_push($allElements, ...$results['results']);
            $next = $results['next'] ?? null;

        } while (null !== $results && !empty($next));

        // when we get elements from the rebrickable api, a single element may appear twice (spare and non-spare)
        // we want to return a deduplicated list of ExternalSetElement, each with their own quantity and spareQuantity
        // therefore we first create a list of spare only elements retrievable by their element_id
        // this list will be used later on final result construction
        $spareElements = [];
        $resultSpareElements = array_filter($allElements, fn ($item) => $item['is_spare'] === true);
        foreach ($resultSpareElements as $element) {
            $spareElements[$element['element_id']] = $element;
        }

        // build the final result with only non-spare elements, adding the spareQuantity if available in the previously
        // built spare elements list

        $finalResults = array_map(
            fn ($item) => new ExternalSetElement(
                $setExternalId,
                $this->loadExternalElement(
                    $item['element_id'] ?? $item['inv_part_id'],
                    $item['part']['part_num'],
                    'https://cdn.rebrickable.com/media/parts/elements/'.$item['element_id'].'.jpg',
                    $item['color']['id']
                ),
                $this->loadExternalPart($item['part']),
                $this->loadExternalColor($item['color']),
                $item['quantity'],
                isset($spareElements[$item['element_id']]) ? $spareElements[$item['element_id']]['quantity'] : 0
            ),
            array_filter($allElements, fn ($item) => $item['is_spare'] === false)
        );

        return new ExternalSetElementCollection($finalResults);
    }

    #[Override]
    public function findSets(string $search): ?SetCollection
    {
        return $this->cacheManager->getSets($search, fn ($s) => $this->fetchSets($s));
    }

    #[Override]
    public function getSet(string $externalSetId): ?ExternalSet
    {
        return $this->cacheManager->getSet($externalSetId, fn ($s) => $this->fetchSet($s));
    }

    #[Override]
    public function findParts(string $search): ?PartCollection
    {
        return $this->cacheManager->getParts($search, fn ($s) => $this->fetchParts($s));
    }

    #[Override]
    public function getSetParts(string $setExternalId): ?ExternalSetElementCollection
    {
        // retrieve with pagination
        return new ExternalSetElementCollection([]);
    }


    #[Override]
    public function getPartElements(string $partExternalId): ?ExternalElementCollection
    {
        return $this->cacheManager->getPartElements($partExternalId, fn ($s) => $this->fetchPartElements($partExternalId));
    }

    #[Override]
    public function getSetElements(string $setExternalId): ?ExternalSetElementCollection
    {
        return $this->cacheManager->getSetElements($setExternalId, fn ($s) => $this->fetchSetElements($setExternalId));
    }
}
