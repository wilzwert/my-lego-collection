<?php

namespace App\User\Domain\Model;

use App\Shared\Domain\Model\Uuid;

readonly class User
{
    /**
     * @param Uuid $id
     * @param Uuid $identityId
     */
    public function __construct(
        private Uuid $id,
        private Uuid $identityId
    ) {
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getIdentityId(): Uuid
    {
        return $this->identityId;
    }
}

