<?php

namespace App\Tests\Utilities;

use MyLegoCollection\SharedContracts\Message;

/**
 * @template T of Message
 * @author Wilhelm Zwertvaegher
 */
interface DummyHandler
{

    /**
     * @return array<T>
     */
    public function getReceivedMessages(): array;

    public function reset(): void;
}
