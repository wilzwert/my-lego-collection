<?php

namespace App\Tests\CollectionManagement\Infrastructure\Service;

use App\CollectionManagement\Infrastructure\Service\ExternalDataCacheManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use PHPUnit\Framework\Attributes\Test;
use App\CollectionManagement\Infrastructure\Service\RebrickableDataLoader;

class RebrickableDataLoaderIT extends KernelTestCase
{

    private ExternalDataCacheManager $cacheManager;

    private RebrickableDataLoader $underTest;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->cacheManager = new ExternalDataCacheManager($container->get('cache.rebrickable_search'));
        $this->cacheManager->clear();
        $httpClient = $container->get(HttpClientInterface::class);
        $this->underTest = new RebrickableDataLoader($this->cacheManager, $httpClient, $_ENV['REBRICKABLE_API_KEY']);
    }

    #[Test]
    public function shouldGetSetsFromExternalApiThenFromCache(): void
    {
        $search = 'Star Wars';
        $sets = $this->underTest->findSets($search);

        self::assertCount(100, $sets);
        self::assertEquals($sets, $this->cacheManager->getSets('STAR WARS', fn ($s) => $this->fail("Should have been cached for $search")));
        self::assertEquals($sets, $this->cacheManager->getSets('Star wars', fn ($s) => $this->fail("Should have been cached for $search")));
    }

    #[Test]
    public function shouldGetOneSetFromExternalApiThenFromCache(): void
    {
        $search = '75353';
        $sets = $this->underTest->findSets($search);

        self::assertCount(1, $sets);
        self::assertEquals($sets, $this->cacheManager->getSets('75353', fn () => $this->fail("Should have been cached")));
        self::assertEquals("75353", $sets->toArray()[0]->getLegoId());
        self::assertEquals("75353-1", $sets->toArray()[0]->getExternalId());
    }

    #[Test]
    public function shouldGetPartsFromExternalApiThenFromCache(): void
    {
        $search = 'Modulex Tile 1 x 2 with Thin Dark Gray';
        $parts = $this->underTest->findParts($search);

        self::assertCount(74, $parts);
        self::assertEquals($parts, $this->cacheManager->getParts('Modulex Tile 1 x 2 with Thin Dark Gray', fn ($s) => $this->fail("Should have been cached for $search")));
        self::assertEquals($parts, $this->cacheManager->getParts('MODULEX TILE 1 X 2 WITH THIN DARK GRAY', fn ($s) => $this->fail("Should have been cached for $search")));
    }

    #[Test]
    public function shouldGetOnePartFromExternalApiThenFromCache(): void
    {
        $search = '93061';
        $parts = $this->underTest->findParts($search);

        self::assertCount(1, $parts);
        self::assertEquals($parts, $this->cacheManager->getParts('93061', fn () => $this->fail("Should have been cached")));
        self::assertEquals("93061", $parts->get(0)->getExternalId());
        self::assertEquals('Arm Skeleton Bent with Clips at 90° [Vertical Grip]', $parts->get(0)->getName());
    }

    #[Test]
    public function shouldGetPartElementsFromExternalApiThenFromCache(): void
    {
        $partExternalId = '93061';
        $elements = $this->underTest->getPartElements($partExternalId);

        self::assertCount(7, $elements);
        self::assertEquals($elements, $this->cacheManager->getPartElements($partExternalId, fn () => $this->fail("Should have been cached")));
        self::assertEquals("6302313", $elements->get(0)->getExternalId());
        self::assertEquals("Black", $elements->get(0)->getColorName());
        self::assertEquals("0", $elements->get(0)->getExternalColorId());
    }

    #[Test]
    public function shouldGetSetElementsFromExternalApiThenFromCache(): void
    {
        $setExternalId = '75353-1';
        $elements = $this->underTest->getSetElements($setExternalId);

        self::assertCount(85, $elements);
        self::assertEquals($elements, $this->cacheManager->getSetElements($setExternalId, fn () => $this->fail("Should have been cached")));
        self::assertEquals("6302313", $elements->get(0)->getExternalId());
        self::assertEquals("75353-1", $elements->get(0)->getExternalSetId());
        self::assertEquals("93061", $elements->get(0)->getExternalPartId());
        self::assertEquals(4, $elements->get(0)->getQuantity());
    }
}
