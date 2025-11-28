<?php

namespace App\Shared\Infrastructure\EventHandler;


use MyLegoCollection\SharedEvent\Command\Command;

/**
 * @template T of Command
 * @extends MessageHandler<T>
 * Interface that must be implemented by all command handlers in infrastructures
 * This will allow writing tests that check a specific mandatory command handler exists in a slice/module,
 * which can also be seen as self documentation / integrity check
 *
 * @author Wilhelm Zwertvaegher
 */
interface CommandHandler extends MessageHandler
{

}
