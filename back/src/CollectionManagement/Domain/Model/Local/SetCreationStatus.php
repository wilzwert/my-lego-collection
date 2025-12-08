<?php

namespace App\CollectionManagement\Domain\Model\Local;

/**
 * @author Wilhelm Zwertvaegher
 */
enum SetCreationStatus: string
{

    // Set has been created, and is awaiting related data creation (e.g. parts...)
    case CREATED = 'created';

    // Set is complete
    case COMPLETED = 'completed';

}
