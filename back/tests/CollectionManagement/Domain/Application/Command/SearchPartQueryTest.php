<?php

namespace App\Tests\CollectionManagement\Domain\Application\Command;

use App\CollectionManagement\Application\Command\SearchPartQuery;
use App\Shared\Domain\Model\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class SearchPartQueryTest extends TestCase
{
    #[Test]
    public function getSearch_shouldReturnExpectedValue(): void
    {
        $query = new SearchPartQuery('brick');

        self::assertSame('brick', $query->getSearch());
    }

    #[Test]
    public function getUserId_shouldReturnNullWhenNotProvided(): void
    {
        $query = new SearchPartQuery('plate');

        self::assertNull($query->getUserId());
    }

    #[Test]
    public function getUserId_shouldReturnExpectedValueWhenProvided(): void
    {
        $userId = $this->createMock(Uuid::class);

        $query = new SearchPartQuery('tile', $userId);

        self::assertSame($userId, $query->getUserId());
    }
}
