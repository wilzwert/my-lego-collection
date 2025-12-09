<?php

namespace App\CollectionManagement\Application\Command;

use App\CollectionManagement\Domain\Model\Local\UserSetStatus;

final readonly class AddUserSetCommand
{
    public function __construct(
        private string $externalSetId,
        private string $identityId,
        private UserSetStatus $status,
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

    public function getStatus(): UserSetStatus
    {
        return $this->status;
    }
}
