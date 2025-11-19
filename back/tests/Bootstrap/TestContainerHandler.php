<?php

namespace App\Tests\Bootstrap;

use PHPUnit\Event\TestRunner\ExecutionStarted;

/**
 * @author Wilhelm Zwertvaegher
 */
interface TestContainerHandler
{
    public function start(): void;
    public function stop(): void;

    public function isStarted(): bool;

    public function getHost(): string;

    public function getEnvVars(): array;
}
