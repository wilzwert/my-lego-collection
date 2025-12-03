<?php

namespace App\Auth\Application\Handler;

use App\Auth\Application\Command\ChangeEmailCommand;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Port\Driven\IdentityRepository;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Exception\EntityNotFoundException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;

readonly class ChangeEmailHandler
{
    public function __construct(
        private IdentityService $identityService,
        private IdentityRepository  $identityRepository,
        private TransactionProvider $transactionProvider,
        private EventBus $eventBus

    ) {
    }

    public function __invoke(ChangeEmailCommand $command): ?Identity
    {
        $identityId = EntityId::fromString($command->identityId);
        $identity = $this->identityRepository->findById($identityId);
        if (null === $identity) {
            throw new EntityNotFoundException("Identity with identifier could not be found");
        }

        return $this->transactionProvider->transactional(function () use ($identity, $command) {
            $identity = $this->identityService->changeEmail($identity, $command->email);
            $this->identityRepository->save($identity);
            $this->eventBus->dispatchAll($identity);
            return $identity;
        });
    }
}
