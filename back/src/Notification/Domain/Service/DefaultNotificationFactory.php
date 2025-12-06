<?php

namespace App\Notification\Domain\Service;

use App\Notification\Domain\Model\Notification;
use App\Notification\Domain\Model\WelcomeNotification;
use App\Notification\Domain\Port\Driven\RetrieveIdentityInfo;
use App\Shared\Domain\Exception\EntityNotFoundException;
use MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand;
use MyLegoCollection\SharedContracts\Message;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class DefaultNotificationFactory implements NotificationFactory
{

    public function __construct(private RetrieveIdentityInfo $retrieveIdentityInfo)
    {
    }

    public function createNotification(Message $message): Notification
    {
        return match ($message::class) {
            SendWelcomeNotificationCommand::class => $this->createWelcomeNotification($message),
            default => throw new \InvalidArgumentException('Unknown command type ' . $message->type()),
        };
    }


    private function createWelcomeNotification(SendWelcomeNotificationCommand $command): WelcomeNotification
    {
        $identityInfo = $this->retrieveIdentityInfo->getIdentityInfoFromId($command->getIdentityId());
        if ($identityInfo === null) {
            throw new EntityNotFoundException('Cannot load IdentityInfo for ' . $command->getIdentityId());
        }
        return new WelcomeNotification($command->id(), $identityInfo, $command->getValidationToken());
    }

}
