<?php

namespace App\Tests\CollectionManagement\Domain\Service;

use App\CollectionManagement\Domain\Model\ExternalSet;
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
            ->willReturn([]);

        $expectedResults = [
            new ExternalSet('externalId1', 'name', 30, 'image1', 2005),
            new ExternalSet('externalId2', 'name2', 40, 'image2', 2006),
        ];
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
}
