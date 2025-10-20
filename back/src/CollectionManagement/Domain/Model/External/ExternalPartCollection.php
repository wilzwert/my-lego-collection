<?php

namespace App\CollectionManagement\Domain\Model\External;

use App\Shared\Domain\Collection;

/**
 * @author W. Zwertvaegher
 * Collection of objects extending Part
 * @see Part
 * @see LocalPart
 * @see ExternalPart
 * @extends Collection<ExternalPart>
 */
final class ExternalPartCollection extends Collection
{
    public function __construct(array $elements = [])
    {
        parent::__construct(ExternalPart::class, $elements);
    }
}
