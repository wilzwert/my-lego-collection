<?php

namespace App\Shared\Infrastructure\EventHandler;


use MyLegoCollection\SharedEvent\Command\Command;

/**
 * Interface that must be implemented by all event handlers in infrastructures
 * This will allow writing tests that check a specific mandatory command handler exists in a slice/module,
 * which can also be seen as self documentation / integrity check
 *
 * @author Wilhelm Zwertvaegher
 */
interface CommandHandler
{
    /**
     * @return class-string<Command> the Command class
     */
    public static function getCommandHandled(): string;

}
