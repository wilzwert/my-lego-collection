<?php

namespace App\Tests\CollectionManagement\Unit\Infrastructure\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
class RebrickableDataFetcherTest
{
    // TODO
    public function todo(): void
    {
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
                $this->stringContains('sets/75353-1'),
                $expectedOptions
            )
            ->willReturn($response);
    }

}
