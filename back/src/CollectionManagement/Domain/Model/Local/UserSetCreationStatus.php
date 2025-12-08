<?php

namespace App\CollectionManagement\Domain\Model\Local;

/**
 * @author Wilhelm Zwertvaegher
 */
enum UserSetCreationStatus: string
{
    // UserSet has been created, and is awaiting related operations (e.g. Set to be completed...)
    case CREATED = 'created';

    // UserSet is complete
    case COMPLETED = 'completed';

}
