<?php

namespace App\CollectionManagement\Domain\Model\External;

use App\Shared\Domain\Collection;

/**
 * @author W. Zwertvaegher
 * Collection of objects extending Part
 * @see Part
 * @see LocalPart
 * @see ExternalPart
 * @extends Collection<ExternalElement>
 */
final class ExternalElementCollection extends Collection
{
    public function __construct(array $elements = [])
    {
        parent::__construct(ExternalElement::class, $elements);
    }
}
