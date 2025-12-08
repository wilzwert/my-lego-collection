<?php

namespace App\CollectionManagement\Application\Handler;

use MyLegoCollection\SharedContracts\Command\CompleteUserSetCommand;

/**
 * @author Wilhelm Zwertvaegher
 */
class CompleteUserSetHandler
{
    public function __invoke(CompleteUserSetCommand $command): void
    {
        // retrieve the UserSet

        // retrieve the SetElements for the Set

        // create UserSetElements from the SetElements

        // set the UserSet creation status COMPLETED

        // dispatch events
    }

}
