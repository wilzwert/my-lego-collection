<?php

namespace App\Notification\Application\Service;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\WelcomeNotification;
use App\Notification\Domain\Service\RetrieveIdentityInfo;
use App\Shared\Domain\Exception\EntityNotFoundException;
use MyLegoCollection\SharedEvent\Command\SendWelcomeNotificationCommand;
use MyLegoCollection\SharedEvent\Message;

/**
 * @author Wilhelm Zwertvaegher
 */
class DefaultNotificationFactory implements NotificationFactory
{

    public function __construct(private readonly RetrieveIdentityInfo $retrieveIdentityInfo)
    {
    }

    public function createNotification(Message $message): Notification
    {
        return match ($message::class) {
            SendWelcomeNotificationCommand::class => $this->createWelcomeNotification($message),
            default => throw new \InvalidArgumentException('Unknown command')
        };
    }


    private function createWelcomeNotification(SendWelcomeNotificationCommand $command): WelcomeNotification
    {
        $identityInfo = $this->retrieveIdentityInfo->getIdentityInfoFromId($command->getIdentityId());
        if ($identityInfo === null) {
            throw new EntityNotFoundException('Cannot load IdentityInfo');
        }
        return new WelcomeNotification($identityInfo, $command->getValidationToken());
    }

}
