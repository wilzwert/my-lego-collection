<?php

namespace App\CollectionManagement\Application\Handler;

use App\CollectionManagement\Application\Command\AddUserSetCommand;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Port\Driven\RetrieveUserId;
use App\CollectionManagement\Domain\Port\Driven\SetRepository;
use App\CollectionManagement\Domain\Port\Driven\UserSetRepository;
use App\CollectionManagement\Domain\Service\SetService;
use App\CollectionManagement\Domain\Service\UserSetService;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Port\Driven\EventBus;
use App\Shared\Domain\Port\Driven\TransactionProvider;

final readonly class AddUserSetHandler
{
    public function __construct(
        private RetrieveUserId      $retrieveUser,
        private SetRepository       $localSetRepository,
        private SetService          $setService,
        private UserSetService      $userSetService,
        private UserSetRepository    $userSetRepository,
        private TransactionProvider $transactionProvider,
        private EventBus            $eventBus
    ) {
    }

    public function __invoke(AddUserSetCommand $command): UserSet
    {
        // get the user id associated to the command's identityId
        $userId = $this->retrieveUser->getUserId(EntityId::fromString($command->getIdentityId()));
        if (null === $userId) {
            throw new EntityNotFoundException('User not found');
        }

        return $this->transactionProvider->transactional(function () use ($command, $userId) {
            // retrieve Set or create it from external source if needed
            $set = $this->localSetRepository->findByExternalId($command->getExternalSetId());

            // create the set if it does not exist and dispatch the create set event(s)
            if (null === $set) {
                $set = $this->setService->createSet($command->getExternalSetId());
                $this->localSetRepository->save($set);
                $this->eventBus->dispatchAll($set);
            }

            $createdUserSet = $this->userSetService->createUserSet($userId, $set,$command->getStatus());
            $this->eventBus->dispatchAll($createdUserSet);
            $this->userSetRepository->save($createdUserSet);
            return $createdUserSet;
        });
    }
}
