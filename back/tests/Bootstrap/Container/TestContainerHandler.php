<?php

namespace App\Tests\Bootstrap\Container;

use Testcontainers\Container\StartedGenericContainer;

/**
 * A Testcontainer handler with utility methods.
 *
 * @author Wilhelm Zwertvaegher
 */
interface TestContainerHandler
{
    public function start(): StartedGenericContainer;

    public function stop(): void;

    public function isStarted(): bool;

    public function getHost(): string;

    /**
     * @return list<string>
     */
    public function getEnvVars(): array;
}
