<?php

namespace App\CollectionManagement\Domain\Model;

use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\Shared\Domain\Collection;

/**
 * @author Wilhelm Zwertvaegher
 * Collection of UserSet

 * @extends Collection<UserSet>
 */
final class UserSetCollection extends Collection
{
    public function __construct(array $elements = [])
    {
        parent::__construct(UserSet::class, $elements);
    }
}
