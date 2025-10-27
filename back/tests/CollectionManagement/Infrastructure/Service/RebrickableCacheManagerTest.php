<?php

namespace App\Tests\CollectionManagement\Infrastructure\Service;

use App\CollectionManagement\Domain\Model\External\ExternalElement;
use App\CollectionManagement\Domain\Model\External\ExternalElementCollection;
use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\External\ExternalSetElement;
use App\CollectionManagement\Domain\Model\External\ExternalSetElementCollection;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Infrastructure\Service\RebrickableCacheManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;

class RebrickableCacheManagerTest extends TestCase
{
    private CacheInterface $cache;
    private RebrickableCacheManager $manager;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->manager = new RebrickableCacheManager($this->cache);
    }

    #[Test]
    public function getSetsShouldCallCacheWithCorrectKeyAndCallback()
    {
        $search = 'Millennium Falcon';
        $expectedKey = 'search_set_' . md5(strtolower($search));
        $expectedSets = new SetCollection([
            new ExternalSet('externalId1', 'legoId1', 'Cached set 1', 100, '', 2005),
            new ExternalSet('externalId2', 'legoId2', 'Cached set 2', 200, '', 2006),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($expectedKey),
                $this->callback(function ($callback) use ($search, $expectedSets) {
                    $item = $this->createMock(ItemInterface::class);
                    $item->expects($this->once())->method('expiresAfter')->with(86400);

                    $result = $callback($item);
                    return $result === $expectedSets;
                })
            )
            ->willReturn($expectedSets);

        $result = $this->manager->getSets($search, fn() => $expectedSets);
        $this->assertSame($expectedSets, $result);
    }

    #[Test]
    public function getPartsShouldCallCacheWithCorrectKeyAndCallback()
    {
        $search = 'Millennium Falcon';
        $expectedKey = 'search_part_' . md5(strtolower($search));
        $expectedParts = new PartCollection([
            new ExternalPart('externalId1', 'legoId1', 'Cached part 1', ''),
            new ExternalPart('externalId2', 'legoId2', 'Cached part 2', ''),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($expectedKey),
                $this->callback(function ($callback) use ($search, $expectedParts) {
                    $item = $this->createMock(ItemInterface::class);
                    $item->expects($this->once())->method('expiresAfter')->with(86400);

                    $result = $callback($item);
                    return $result === $expectedParts;
                })
            )
            ->willReturn($expectedParts);

        $result = $this->manager->getParts($search, fn() => $expectedParts);
        $this->assertSame($expectedParts, $result);
    }

    #[Test]
    public function getPartsShouldUseCorrectCacheKey()
    {
        $search = 'Brick';
        $expectedKey = 'search_part_' . md5(strtolower($search));

        $expectedParts = new PartCollection([
            new ExternalPart('externalId1', 'legoId1', 'Cached part 1', ''),
            new ExternalPart('externalId2', 'legoId2', 'Cached part 2', ''),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo($expectedKey), $this->anything())
            ->willReturn($expectedParts);

        $this->manager->getParts($search, fn() => $expectedParts);
    }

    #[Test]
    public function getPartElementsShouldUseCorrectCacheKey()
    {
        $id = '3001';
        $expectedKey = 'get_part_elements' . md5(strtolower($id));

        $expectedElements = new ExternalElementCollection([
            new ExternalElement('externalId1', 'legoId1', 'externalPartId1', '', 0, 'Black'),
            new ExternalElement('externalId2', 'legoId2', 'externalPartId2', '', 4, 'Red'),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo($expectedKey), $this->anything())
            ->willReturn($expectedElements);

        $this->manager->getPartElements($id, fn() => $expectedElements);
    }

    #[Test]
    public function getSetElementsShouldUseCorrectCacheKey()
    {
        $id = '75257';
        $expectedKey = 'get_set_elements' . md5(strtolower($id));

        $expectedElements = new ExternalSetElementCollection([
            new ExternalSetElement('externalId1', '93061', 'externalPartId1', 5),
            new ExternalSetElement('externalId2', '93061', 'externalPartId2', 10),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo($expectedKey), $this->anything())
            ->willReturn($expectedElements);

        $this->manager->getSetElements($id, fn() => $expectedElements);
    }

    #[Test]
    public function whenCacheIsAbstractAdapter_thenShouldClear()
    {
        $adapter = $this->createMock(AbstractAdapter::class);
        $adapter->expects($this->once())->method('clear');

        $manager = new RebrickableCacheManager($adapter);
        $manager->clear();
    }

    #[Test]
    public function whenCacheIsNotAbstractAdapter_thenClearShouldDoNothing()
    {
        $adapter = $this->createMock(MockCacheInterfaceImplementation::class);
        $manager = new RebrickableCacheManager($adapter);

        $adapter->expects($this->never())->method('clear');
        $this->manager->clear();
    }
}
