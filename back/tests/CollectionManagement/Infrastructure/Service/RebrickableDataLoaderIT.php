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
        $this->assertEquals("75353", $sets->toArray()[0]->getLegoId());
        $this->assertEquals("75353-1", $sets->toArray()[0]->getExternalId());
    }

    #[Test]
    public function shouldGetPartsFromExternalApiThenFromCache(): void
    {
        $search = 'Modulex Tile 1 x 2 with Thin Dark Gray';
        $parts = $this->underTest->findParts($search);

        $this->assertCount(74, $parts);
        $this->assertEquals($parts, $this->cacheManager->getParts('Modulex Tile 1 x 2 with Thin Dark Gray', fn($s) => $this->fail("Should have been cached for $search")));
        $this->assertEquals($parts, $this->cacheManager->getParts('MODULEX TILE 1 X 2 WITH THIN DARK GRAY', fn($s) => $this->fail("Should have been cached for $search")));
    }

    #[Test]
    public function shouldGetOnePartFromExternalApiThenFromCache(): void
    {
        $search = '93061';
        $parts = $this->underTest->findParts($search);

        $this->assertCount(1, $parts);
        $this->assertEquals($parts, $this->cacheManager->getParts('93061', function () use ($search) {$this->fail("Should have been cached");}));
        $this->assertEquals("93061", $parts->get(0)->getExternalId());
        $this->assertEquals('Arm Skeleton Bent with Clips at 90° [Vertical Grip]', $parts->get(0)->getName());
    }

    #[Test]
    public function shouldGetPartElementsFromExternalApiThenFromCache(): void
    {
        $partExternalId = '93061';
        $elements = $this->underTest->getPartElements($partExternalId);

        $this->assertCount(7, $elements);
        $this->assertEquals($elements, $this->cacheManager->getPartElements($partExternalId, function () use ($partExternalId) {$this->fail("Should have been cached");}));
        $this->assertEquals("6302313", $elements->get(0)->getExternalId());
        $this->assertEquals("Black", $elements->get(0)->getColorName());
        $this->assertEquals("0", $elements->get(0)->getExternalColorId());
    }

    #[Test]
    public function shouldGetSetElementsFromExternalApiThenFromCache(): void
    {
        $setExternalId = '75353-1';
        $elements = $this->underTest->getSetElements($setExternalId);

        $this->assertCount(86, $elements);
        $this->assertEquals($elements, $this->cacheManager->getSetElements($setExternalId, function () use ($setExternalId) {$this->fail("Should have been cached");}));
        $this->assertEquals("6302313", $elements->get(0)->getExternalId());
        $this->assertEquals("75353-1", $elements->get(0)->getExternalSetId());
        $this->assertEquals("93061", $elements->get(0)->getExternalPartId());
        $this->assertEquals(4, $elements->get(0)->getQuantity());
    }
}
