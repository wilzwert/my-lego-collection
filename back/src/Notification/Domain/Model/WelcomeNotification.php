<?php

namespace App\Notification\Domain\Model;

/**
 * @author Wilhelm Zwertvaegher
 */
final class WelcomeNotification extends Notification
{
    public function __construct(IdentityInfo $identityInfo, string $validationToken)
    {
        parent::__construct(
            $identityInfo,
            NotificationType::WELCOME,
            ['username' => $identityInfo->getUsername(), 'validationToken' => $validationToken]
        );
    }
}
