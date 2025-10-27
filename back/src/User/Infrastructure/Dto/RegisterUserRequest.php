<?php

namespace App\User\Infrastructure\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserRequest
{

    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    private readonly string $email;

    #[Assert\Regex(
        pattern: '/[@ ]/',
        match: false,
        message: "The username cannot include spaces or '@'.",
    )]
    private readonly string $username;

    #[Assert\PasswordStrength]
    private readonly string $password;
    public function __construct(
        string $email,
        string $username,
        string $password
    )
    {
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

}
