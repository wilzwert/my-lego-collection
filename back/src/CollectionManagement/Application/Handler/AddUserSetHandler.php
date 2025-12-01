<?php

namespace App\CollectionManagement\Application\Handler;

use App\CollectionManagement\Application\Command\AddUserSetCommand;
use App\CollectionManagement\Application\Command\SearchSetQuery;
use App\CollectionManagement\Domain\Model\EnrichedSetCollection;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Service\RetrieveUserId;
use App\CollectionManagement\Domain\Service\SetService;
use App\Shared\Domain\Model\EntityId;
use App\User\Domain\Model\User;

final readonly class AddUserSetHandler
{
    public function __construct(private readonly RetrieveUserId $retrieveUser)
    {
    }

    public function __invoke(AddUserSetCommand $command): UserSet
    {
        // get the user id associated to the command's identityId

        $userId = $this->retrieveUser->getUserId($command->getIdentityId());

        // add the event(s) to the message bus

        // TODO
        return new UserSet(
            EntityId::generate(),
            $userId,
            new Set(EntityId::generate(), 'externalId', 'legoId', 'name', 10, '', 2005)
        );
    }
}
