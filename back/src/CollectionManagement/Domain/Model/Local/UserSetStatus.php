<?php

namespace App\CollectionManagement\Domain\Model\Local;

/**
 * @author Wilhelm Zwertvaegher
 */
enum UserSetStatus: string
{
    case WANTED = 'wanted';
    case OWNED = 'owned';
    case BUILT = 'built';

}
