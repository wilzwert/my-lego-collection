<?php

namespace App\Tests\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\External\ExternalPartCollection;
use App\CollectionManagement\Domain\Model\SetCollection;
use App\CollectionManagement\Domain\Service\LegoDataLoader;
use App\CollectionManagement\Domain\Service\LegoDataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LegoDataProviderTest extends TestCase
{
    #[Test]
    public function shouldGetSetsFoundInFirstNonEmptyLoader() :void
    {
        $loaderMock1 = $this->createMock(LegoDataLoader::class);
        $loaderMock1->expects($this->once())
            ->method('findSets')
            ->with('search')
            ->willReturn(null);

        $expectedResults = new SetCollection([
            new ExternalSet('legoId1', 'externalId1', 'name', 30, 'image1', 2005),
            new ExternalSet('legoId2', 'externalId2', 'name2', 40, 'image2', 2006),
        ]);

        $loaderMock2 = $this->createMock(LegoDataLoader::class);
        $loaderMock2->expects($this->once())
            ->method('findSets')
            ->with('search')
            ->willReturn($expectedResults);
        $loaderMock3 = $this->createMock(LegoDataLoader::class);
        $loaderMock3->expects($this->never())
            ->method('findSets');
        $provider = new LegoDataProvider([$loaderMock1, $loaderMock2, $loaderMock3]);

        $result = $provider->findSets('search');

        $this->assertInstanceOf(SetCollection::class, $result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function shouldGetPartsFoundInFirstNonEmptyLoader() :void
    {
        $loaderMock1 = $this->createMock(LegoDataLoader::class);
        $loaderMock1->expects($this->once())
            ->method('findParts')
            ->with('search')
            ->willReturn(null);

        $expectedResults = new ExternalPartCollection([
            new ExternalPart('legoId1', 'externalId1', 'name', 'image1'),
            new ExternalPart('legoId2', 'externalId2', 'name2', 'image2'),
        ]);

        $loaderMock2 = $this->createMock(LegoDataLoader::class);
        $loaderMock2->expects($this->once())
            ->method('findParts')
            ->with('search')
            ->willReturn($expectedResults);
        $loaderMock3 = $this->createMock(LegoDataLoader::class);
        $loaderMock3->expects($this->never())
            ->method('findParts');
        $provider = new LegoDataProvider([$loaderMock1, $loaderMock2, $loaderMock3]);

        $result = $provider->findParts('search');

        $this->assertInstanceOf(ExternalPartCollection::class, $result);
        $this->assertCount(2, $result);
    }
}
