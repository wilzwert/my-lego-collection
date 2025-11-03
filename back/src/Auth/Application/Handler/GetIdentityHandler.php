<?php

namespace App\Auth\Application\Handler;

use App\Auth\Application\Command\GetIdentityQuery;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Service\IdentityService;
use App\Shared\Domain\Model\Uuid;
use App\User\Application\Command\GetUserQuery;

readonly class GetIdentityHandler
{
    public function __construct(
        private IdentityService $identityService
    ) {
    }

    public function __invoke(GetIdentityQuery $query): ?Identity
    {
        return $this->identityService->getIdentityById(Uuid::fromString($query->getId()));
    }
}
