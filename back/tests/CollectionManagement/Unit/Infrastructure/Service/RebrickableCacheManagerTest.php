<?php

namespace App\Tests\CollectionManagement\Unit\Infrastructure\Service;

use App\CollectionManagement\Domain\Model\External\ExternalElement;
use App\CollectionManagement\Domain\Model\External\ExternalElementCollection;
use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\External\ExternalSetElement;
use App\CollectionManagement\Domain\Model\External\ExternalSetElementCollection;
use App\CollectionManagement\Domain\Model\PartCollection;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Infrastructure\Service\ExternalDataCacheManager;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Group('Rebrickable')]
class RebrickableCacheManagerTest extends TestCase
{
    private MockObject $cache;
    private ExternalDataCacheManager $manager;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->manager = new ExternalDataCacheManager($this->cache);
    }

    private function expectedHash(string $key): string
    {
        return hash('sha256', strtolower($key));
    }

    #[Test]
    public function getSetsShouldCallCacheWithCorrectKeyAndCallback(): void
    {
        $search = 'Millennium Falcon';
        $expectedKey = 'search_set_' . $this->expectedHash($search);
        $expectedSets = new SetCollection([
            new ExternalSet('externalId1', 'legoId1', 'Cached set 1', 100, '', 2005),
            new ExternalSet('externalId2', 'legoId2', 'Cached set 2', 200, '', 2006),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($expectedKey),
                $this->callback(function ($callback) use ($expectedSets) {
                    $item = $this->createMock(ItemInterface::class);
                    $item->expects($this->once())->method('expiresAfter')->with(86400);

                    $result = $callback($item);
                    return $result === $expectedSets;
                })
            )
            ->willReturn($expectedSets);

        $result = $this->manager->getSets($search, fn () => $expectedSets);
        self::assertSame($expectedSets, $result);
    }

    #[Test]
    public function getPartsShouldCallCacheWithCorrectKeyAndCallback(): void
    {
        $search = 'Millennium Falcon';
        $expectedKey = 'search_part_' . $this->expectedHash($search);
        $expectedParts = new PartCollection([
            new ExternalPart('externalId1', 'legoId1', 'Cached part 1', ''),
            new ExternalPart('externalId2', 'legoId2', 'Cached part 2', ''),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($expectedKey),
                $this->callback(function ($callback) use ($expectedParts) {
                    $item = $this->createMock(ItemInterface::class);
                    $item->expects($this->once())->method('expiresAfter')->with(86400);

                    $result = $callback($item);
                    return $result === $expectedParts;
                })
            )
            ->willReturn($expectedParts);

        $result = $this->manager->getParts($search, fn () => $expectedParts);
        self::assertSame($expectedParts, $result);
    }

    #[Test]
    public function getPartsShouldUseCorrectCacheKey(): void
    {
        $search = 'Brick';
        $expectedKey = 'search_part_' . $this->expectedHash($search);

        $expectedParts = new PartCollection([
            new ExternalPart('externalId1', 'legoId1', 'Cached part 1', ''),
            new ExternalPart('externalId2', 'legoId2', 'Cached part 2', ''),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo($expectedKey), $this->anything())
            ->willReturn($expectedParts);

        $this->manager->getParts($search, fn () => $expectedParts);
    }

    #[Test]
    public function getPartElementsShouldUseCorrectCacheKey(): void
    {
        $id = '3001';
        $expectedKey = 'get_part_elements' . $this->expectedHash($id);

        $expectedElements = new ExternalElementCollection([
            new ExternalElement('externalId1', 'legoId1', 'externalPartId1', '', '0', 'Black'),
            new ExternalElement('externalId2', 'legoId2', 'externalPartId2', '', '4', 'Red'),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo($expectedKey), $this->anything())
            ->willReturn($expectedElements);

        $this->manager->getPartElements($id, fn () => $expectedElements);
    }

    #[Test]
    public function getSetElementsShouldUseCorrectCacheKey(): void
    {
        $id = '75257';
        $expectedKey = 'get_set_elements' . $this->expectedHash($id);

        $expectedElements = new ExternalSetElementCollection([
            new ExternalSetElement('externalId1', '93061', 'externalPartId1', 5),
            new ExternalSetElement('externalId2', '93061', 'externalPartId2', 10),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->equalTo($expectedKey), $this->anything())
            ->willReturn($expectedElements);

        $this->manager->getSetElements($id, fn () => $expectedElements);
    }

    #[Test]
    public function whenCacheIsAbstractAdapter_thenShouldClear(): void
    {
        $this->cache->expects($this->never())->method('get');
        $adapter = $this->createMock(AbstractAdapter::class);
        $adapter->expects($this->once())->method('clear');

        $manager = new ExternalDataCacheManager($adapter);
        $manager->clear();
    }

    #[Test]
    public function whenCacheIsNotAbstractAdapter_thenClearShouldDoNothing(): void
    {
        $adapter = $this->createMock(MockCacheInterfaceImplementation::class);
        $manager = new ExternalDataCacheManager($adapter);

        $this->cache->expects($this->never())->method('get');
        $adapter->expects($this->never())->method('clear');
        $manager->clear();
    }
}
