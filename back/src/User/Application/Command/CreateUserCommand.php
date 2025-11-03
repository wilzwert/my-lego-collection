<?php

namespace App\User\Application\Command;

final class CreateUserCommand
{
    public function __construct(
        public string $identityId {
            get {
                return $this->identityId;
            }
        }
    ) {
    }
}
