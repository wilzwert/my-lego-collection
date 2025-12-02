<?php

namespace App\Auth\Application\Handler;

use App\Auth\Application\Command\RegistrationCommand;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\EventBus;
use App\Shared\Domain\Service\TransactionProvider;

readonly class RegistrationHandler
{
    public function __construct(
        private IdentityService $identityService,
        private IdentityRepository  $identityRepository,
        private TransactionProvider $transactionProvider,
        private EventBus        $eventBus
    ) {
    }

    public function __invoke(RegistrationCommand $command): void
    {
        $this->transactionProvider->transactional(function () use ($command) {
            $identity = $this->identityRepository->findByEmailOrUsername($command->email, $command->username);
            if ($identity) {
                throw new EntityAlreadyExistsException('Identity already exists');
            }
            $identity = $this->identityService->createIdentity($command->email, $command->username, $command->password);
            $this->identityRepository->save($identity);
            $this->eventBus->dispatchAll($identity);
        });
    }
}
