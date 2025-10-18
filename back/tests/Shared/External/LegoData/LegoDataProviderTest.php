<?php

namespace App\Tests\Shared\External\LegoData;

use App\CollectionManagement\Domain\Service\LegoDataLoader;
use App\CollectionManagement\Domain\Service\LegoDataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LegoDataProviderTest extends TestCase
{

    private LegoDataProvider $legoDataProvider;

    protected function setUp(): void
    {
        $loaderMock = $this->createMock(LegoDataLoader::class);

        $this->legoDataProvider = new LegoDataProvider([$loaderMock]);
    }


    #[Test]
    public function shouldGetParts(): void
    {
        $this->assertEquals([], $this->legoDataProvider->findParts("thing"));
    }

    #[Test]
    public function shouldGetSets(): void
    {
        $this->assertEquals([], $this->legoDataProvider->findSets("thing"));
    }
}
