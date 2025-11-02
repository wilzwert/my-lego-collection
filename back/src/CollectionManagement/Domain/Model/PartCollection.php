<?php

namespace App\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\Shared\Domain\Model\Collection;

/**
 * @author Wilhelm Zwertvaegher
 * Collection of objects extending Part
 * @see Part
 * @see LocalPart
 * @see ExternalPart
 * @extends Collection<BasePart>
 */
final class PartCollection extends Collection
{
    public function __construct(array $elements = [])
    {
        parent::__construct(BasePart::class, $elements);
    }
}
