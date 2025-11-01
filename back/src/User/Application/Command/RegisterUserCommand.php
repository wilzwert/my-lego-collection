<?php

namespace App\User\Application\Command;

final class RegisterUserCommand
{
    public function __construct(
        public string $email {
            get {
                return $this->email;
            }
        },
        public string $username {
            get {
                return $this->username;
            }
        },
        public string $password {
            get {
                return $this->password;
            }
        }
    ) {
    }
}
