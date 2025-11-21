<?php

namespace App\Tests\Bootstrap;

/**
 * A Testcontainer handler with utility methods.
 *
 * @author Wilhelm Zwertvaegher
 */
interface TestContainerHandler
{
    public function start(): void;

    public function stop(): void;

    public function isStarted(): bool;

    public function getHost(): string;

    /**
     * @return list<string>
     */
    public function getEnvVars(): array;
}
