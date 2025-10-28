<?php

namespace App\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\External\ExternalSet;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\Shared\Domain\Collection;

/**
 * @author Wilhelm Zwertvaegher
 * Collection of objects extending BaseSet
 * @see BaseSet
 * @see Set
 * @see ExternalSet
 * @extends Collection<BaseSet>
 */
final class SetCollection extends Collection
{
    public function __construct(array $elements = [])
    {
        parent::__construct(BaseSet::class, $elements);
    }
}
