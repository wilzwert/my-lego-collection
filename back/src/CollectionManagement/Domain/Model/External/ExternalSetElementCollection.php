<?php

namespace App\CollectionManagement\Domain\Model\External;

use App\Shared\Domain\Collection;

/**
 * @author W. Zwertvaegher
 * @extends Collection<ExternalSetElement>
 */
final class ExternalSetElementCollection extends Collection
{
    public function __construct(array $elements = [])
    {
        parent::__construct(ExternalSetElement::class,  $elements);
    }
}
