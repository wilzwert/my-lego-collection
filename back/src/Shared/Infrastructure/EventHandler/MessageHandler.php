<?php

namespace App\Shared\Infrastructure\EventHandler;


use MyLegoCollection\SharedContracts\Message;

/**
 * @template T of Message
 * Base interface that must be extended by more specific event/command handlers interfaces in infrastructures
 * This will allow writing tests that check a specific mandatory handler exists in a slice/module,
 * which can also be seen as self documentation / integrity check
 *
 * @author Wilhelm Zwertvaegher
 */
interface MessageHandler
{
    /**
     * @return class-string<T> the class handled
     */
    public static function getMessageHandled(): string;


}
