<?php

namespace App\Tests\CollectionManagement\Infrastructure\Service;

use App\CollectionManagement\Infrastructure\Service\RebrickableCacheManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use PHPUnit\Framework\Attributes\Test;
use App\CollectionManagement\Infrastructure\Service\RebrickableDataLoader;

class RebrickableDataLoaderIT extends KernelTestCase
{

    private RebrickableCacheManager $cacheManager;

    private RebrickableDataLoader $underTest;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->cacheManager = new RebrickableCacheManager($container->get('cache.rebrickable_search'));
        $this->cacheManager->clear();
        $this->httpClient = $container->get(HttpClientInterface::class);
        $this->underTest = new RebrickableDataLoader($this->cacheManager, $this->httpClient, $_ENV['REBRICKABLE_API_KEY']);
    }

    #[Test]
    public function shouldGetSetsFromExternalApiThenFromCache(): void
    {
        $search = 'Star Wars';
        $sets = $this->underTest->findSets($search);

        $this->assertCount(100, $sets);
        $this->assertEquals($sets, $this->cacheManager->getSets('STAR WARS', fn($s) => $this->fail("Should have been cached for $search")));
        $this->assertEquals($sets, $this->cacheManager->getSets('Star wars', fn($s) => $this->fail("Should have been cached for $search")));
    }

    #[Test]
    public function shouldGetOneSetFromExternalApiThenFromCache(): void
    {
        $search = '75353';
        $sets = $this->underTest->findSets($search);

        $this->assertCount(1, $sets);
        $this->assertEquals($sets, $this->cacheManager->getSets('75353', function () use ($search) {$this->fail("Should have been cached");}));
        $this->assertEquals("75353", $sets[0]->getLegoId());
        $this->assertEquals("75353-1", $sets[0]->getExternalId());
    }
}
