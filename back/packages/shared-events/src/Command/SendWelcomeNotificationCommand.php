<?php

namespace MyLegoCollection\SharedEvent\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
class SendWelcomeNotificationCommand extends Command
{

    private const string TYPE = 'welcome.notification';

    public function __construct(
        private readonly string $email,
        private readonly string $username,
        private readonly string $validationToken,
        ?array $metadata = null)
    {
        parent::__construct(self::TYPE, $metadata);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getValidationToken(): string
    {
        return $this->validationToken;
    }
}
