<?php

namespace App\CollectionManagement\Domain\Model\Local;

use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
class UserElement
{
    private readonly EntityId $id;

    private readonly EntityId $elementId;

    private readonly int $setCount;

    private readonly int $spareCount;
}
