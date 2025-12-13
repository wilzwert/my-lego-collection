<?php

namespace App\Tests\CollectionManagement\Unit\Infrastructure\Service;

use App\CollectionManagement\Infrastructure\Service\RebrickableDataFetcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
class RebrickableDataFetcherTest extends TestCase
{
    #[Test]
    public function shouldReturnResponseAsArray(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('toArray')->willReturn(
            ['results' => ['id' => '123456']]
        );

        $expectedOptions = [
            'headers' => [
                'Authorization' => 'key FAKE_API_KEY',
            ],
        ];
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://rebrickable.com/api/v3/lego/sets/75353-1',
                $expectedOptions
            )
            ->willReturn($response);

        $rebrickableDataFetcher = new RebrickableDataFetcher($httpClient, 'FAKE_API_KEY');
        $result = $rebrickableDataFetcher->fetchFromApi('sets/75353-1');

        self::assertEquals(['results' => ['id' => '123456']], $result);
    }

}
