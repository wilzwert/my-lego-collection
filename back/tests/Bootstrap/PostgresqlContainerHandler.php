<?php

namespace App\Tests\Bootstrap;

use Testcontainers\Container\StartedGenericContainer;
use Testcontainers\Modules\PostgresContainer;

/**
 * @author Wilhelm Zwertvaegher
 */

final class PostgresqlContainerHandler implements TestContainerHandler
{
    private const string POSTGRESQL_VERSION = '8.0';

    private ?StartedGenericContainer $container = null;

    public function start(): void
    {
        if (!$this->container instanceof StartedGenericContainer) {
            $container = new PostgresContainer(self::POSTGRESQL_VERSION);
            $this->container = $container->start();
        }
    }

    public function stop(): void
    {
        if ($this->container instanceof StartedGenericContainer) {
            $this->container->stop();
        }
    }

    public function isStarted(): bool
    {
        return $this->container instanceof StartedGenericContainer;
    }
}
