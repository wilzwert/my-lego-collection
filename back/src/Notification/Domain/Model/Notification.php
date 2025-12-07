<?php

namespace App\Notification\Domain\Model;

/**
 * Generic Notification object that will be passed to infrastructure to be sent
 *
 * @author Wilhelm Zwertvaegher
 */
abstract class Notification
{

    /**
     * @param string $messageId
     * @param IdentityInfo $identityInfo
     * @param NotificationType $type
     * @param array<string, string|int> $payload
     */
    public function __construct(
        private readonly string $messageId,
        private readonly IdentityInfo $identityInfo,
        private readonly NotificationType $type,
        private readonly array $payload
    ) {
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getIdentityInfo(): IdentityInfo
    {
        return $this->identityInfo;
    }

    public function getType(): NotificationType
    {
        return $this->type;
    }

    /**
     * @return array<string, string|int>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
