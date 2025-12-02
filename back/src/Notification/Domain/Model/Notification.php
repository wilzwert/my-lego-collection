<?php

namespace App\Notification\Domain\Model;

/**
 * Generic Notification object that will be passed to infrastructure to be sent
 *
 * @author Wilhelm Zwertvaegher
 */
abstract class Notification
{

    public function __construct(
        private readonly IdentityInfo $identityInfo,
        private readonly NotificationType $type,
        private readonly array $payload
    ) {
    }
    public function getIdentityInfo(): IdentityInfo
    {
        return $this->identityInfo;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
