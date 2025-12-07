<?php

namespace MyLegoCollection\SharedContracts\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
class SendWelcomeNotificationCommand extends Command
{

    private const string TYPE = 'welcome.notification';

    public function __construct(
        private readonly string $identityId,
        private readonly string $validationToken,
        ?array $metadata = null
    )
    {
        parent::__construct(self::TYPE, $metadata);
    }

    public function getIdentityId(): string
    {
        return $this->identityId;
    }

    public function getValidationToken(): string
    {
        return $this->validationToken;
    }
}
