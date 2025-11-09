<?php

namespace App\User\Application\Command;

final readonly class GetUserByIdentityQuery
{
    public function __construct(
        public string $identityId
    ) {
    }
}
