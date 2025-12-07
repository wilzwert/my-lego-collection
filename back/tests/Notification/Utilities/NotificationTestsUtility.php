<?php

namespace App\Tests\Notification\Utilities;

use App\Shared\Domain\Model\EntityId;
use App\Tests\Utilities\TestData;
use MyLegoCollection\SharedContracts\Command\SendWelcomeNotificationCommand;
use MyLegoCollection\SharedContracts\Message;
use function Clue\StreamFilter\remove;

/**
 * @author Wilhelm Zwertvaegher
 */
class NotificationTestsUtility
{

    private const string DEFAULT_VALIDATION_TOKEN = 'validation-token';

    /**
     * @template T of Message
     * @param T $message
     * @return T
     */
    private static function patchMessageId(Message $message, string $messageId): Message
    {
        $serializedMessage = serialize($message);

        preg_match('/Message(.+)id";s:36:"[^"]+"/i', $serializedMessage, $matches);
        preg_match('/Message(.)id";s:36:"[^"]+"/i', $serializedMessage, $matches);

        $patchedSerializedMessage = preg_replace(
            '/Message(.)id";s:36:"[^"]+"/i',
            'Message$1id";s:36:"' . $messageId . '"',
            $serializedMessage
        );
        return unserialize($patchedSerializedMessage);
    }

    public static function generateSendWelcomeNotificationCommand(
        ?EntityId $entityId = null,
        ?string   $validationToken = null,
        ?string   $messageId = null
    ): SendWelcomeNotificationCommand
    {
        $command = new SendWelcomeNotificationCommand(
            $entityId ?? EntityId::generate(),
            $validationToken ?? self::DEFAULT_VALIDATION_TOKEN
        );
        if (null === $messageId) {
            return $command;
        }

        $result = self::patchMessageId($command, $messageId);

        // force type checking for phpstan
        if ($result instanceof SendWelcomeNotificationCommand) {
            return $result;
        }
        throw new \RuntimeException('Unable to generate appropriate SendWelcomeNotificationCommand');
    }

    public static function generateSentSendWelcomeNotificationCommand(
        ?EntityId $entityId = null,
        ?string   $validationToken = null
    ): SendWelcomeNotificationCommand
    {
        return self::generateSendWelcomeNotificationCommand(
            $entityId ?? EntityId::fromString(TestData::EXISTING_IDENTITY_USER1_ID),
            $validationToken,
                TestData::EXISTING_USER1_SENT_EMAIL_WELCOME_MESSAGE_ID
        );
    }

}
