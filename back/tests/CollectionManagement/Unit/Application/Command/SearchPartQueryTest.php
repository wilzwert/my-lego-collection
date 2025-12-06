<?php

namespace App\Tests\CollectionManagement\Unit\Application\Command;

use App\CollectionManagement\Application\Command\SearchPartQuery;
use App\Shared\Domain\Model\EntityId;
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
        $userId = $this->createStub(EntityId::class);

        $query = new SearchPartQuery('tile', $userId);

        self::assertSame($userId, $query->getUserId());
    }
}
