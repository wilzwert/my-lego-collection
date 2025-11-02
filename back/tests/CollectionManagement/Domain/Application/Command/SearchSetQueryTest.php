<?php

namespace App\Tests\CollectionManagement\Domain\Application\Command;

use App\CollectionManagement\Application\Command\SearchSetQuery;
use App\Shared\Domain\Model\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class SearchSetQueryTest extends TestCase
{
    #[Test]
    public function getSearch_shouldReturnExpectedValue(): void
    {
        $query = new SearchSetQuery('brick');

        self::assertSame('brick', $query->getSearch());
    }

    #[Test]
    public function getUserId_shouldReturnNullWhenNotProvided(): void
    {
        $query = new SearchSetQuery('plate');

        self::assertNull($query->getUserId());
    }

    #[Test]
    public function getUserId_shouldReturnExpectedValueWhenProvided(): void
    {
        $userId = $this->createMock(Uuid::class);

        $query = new SearchSetQuery('tile', $userId);

        self::assertSame($userId, $query->getUserId());
    }
}
