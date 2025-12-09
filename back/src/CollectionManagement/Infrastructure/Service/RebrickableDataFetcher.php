<?php

namespace App\CollectionManagement\Infrastructure\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
class RebrickableDataFetcher
{

    private const API_BASE_URL = 'https://rebrickable.com/api/v3/lego/';

    public function __construct(
        private readonly HttpClientInterface      $httpClient,
        private readonly string                   $apiKey
    ) {
    }

    /**
     * @param string $endpointUri
     * @return array<mixed>|null
     *
     * */
    public function fetchFromApi(string $endpointUri): ?array
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
            return $response->toArray();
        }
            // TODO : properly handle this throwable
        catch (\Throwable $e) {
            return null;
        }
    }

}
