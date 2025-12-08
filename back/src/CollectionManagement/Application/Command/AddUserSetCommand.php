<?php

namespace App\CollectionManagement\Application\Command;

use App\Shared\Domain\Model\EntityId;

final readonly class AddUserSetCommand
{
    public function __construct(
        private string $externalSetId,
        private string $identityId
    ) {
    }

    public function getExternalSetId(): string
    {
        return $this->externalSetId;
    }

    public function getIdentityId(): string
    {
        return $this->identityId;
    }
}
