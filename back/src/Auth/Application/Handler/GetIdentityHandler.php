<?php

namespace App\Auth\Application\Handler;

use App\Auth\Application\Command\GetIdentityQuery;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Shared\Domain\Model\EntityId;

readonly class GetIdentityHandler
{
    public function __construct(
        private IdentityRepository $identityRepository,
    ) {
    }

    public function __invoke(GetIdentityQuery $query): ?Identity
    {
        return $this->identityRepository->findById(EntityId::fromString($query->id));
    }
}
