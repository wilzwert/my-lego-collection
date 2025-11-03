<?php

namespace App\Auth\Application\Command;

final class RegistrationCommand
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
