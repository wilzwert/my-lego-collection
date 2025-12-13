<?php

namespace App\Tests\CollectionManagement\Unit\Domain;

use App\CollectionManagement\Domain\Model\Local\UserElement;
use App\Shared\Domain\Model\EntityId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserElementTest extends TestCase
{

    #[Test]
    public function shouldConstruct(): void
    {
        $userElementId = EntityId::generate();
        $userId = EntityId::generate();
        $elementId = EntityId::generate();

        $userElement = new UserElement($userElementId, $userId, $elementId, 2, 1);

        self::assertTrue($userElementId->equals($userElement->getId()));
        self::assertTrue($userId->equals($userElement->getUserId()));
        self::assertTrue($elementId->equals($userElement->getElementId()));
        self::assertEquals(2, $userElement->getSetCount());
        self::assertEquals(1, $userElement->getSpareCount());
    }

    #[Test]
    public function whenAddZero_thenShouldReturnSameInstance(): void
    {
        $userElementId = EntityId::generate();
        $userId = EntityId::generate();
        $elementId = EntityId::generate();

        $userElement = new UserElement($userElementId, $userId, $elementId, 2, 1);

        $updatedUserElement = $userElement->updateCount(0, 0);

        self::assertSame($userElement, $updatedUserElement);
    }

    #[Test]
    public function whenSetCountNegativeAndBiggerThanCurrentSpareCount_thenAddShouldThrowInvalidArgumentException(): void
    {
        $userElementId = EntityId::generate();
        $userId = EntityId::generate();
        $elementId = EntityId::generate();

        $userElement = new UserElement($userElementId, $userId, $elementId, 2, 1);

        self::expectException(\InvalidArgumentException::class);
        $userElement->updateCount(-3, 2);
    }

    #[Test]
    public function whenSpareCountNegativeAndBiggerThanCurrentSpareCount_thenAddShouldThrowInvalidArgumentException(): void
    {
        $userElementId = EntityId::generate();
        $userId = EntityId::generate();
        $elementId = EntityId::generate();

        $userElement = new UserElement($userElementId, $userId, $elementId, 2, 1);

        self::expectException(\InvalidArgumentException::class);
        $userElement->updateCount(2, -2);
    }

    #[Test]
    public function shouldUpdateCount(): void
    {

        $userElementId = EntityId::generate();
        $userId = EntityId::generate();
        $elementId = EntityId::generate();

        $userElement = new UserElement($userElementId, $userId, $elementId, 5, 2);

        $updatedUserElement = $userElement->updateCount(3, -1);

        self::assertNotSame($userElement, $updatedUserElement);
        self::assertEquals(8, $updatedUserElement->getSetCount());
        self::assertEquals(1, $updatedUserElement->getSpareCount());
    }
}
