<?php

namespace App\Tests\Bootstrap;

use Testcontainers\Container\GenericContainer;
use Testcontainers\Modules\PostgresContainer;
use Testcontainers\Wait\WaitForLog;

/**
 * @author Wilhelm Zwertvaegher
 */

final class PostgresqlContainerHandler extends AbstractTestContainerHandler
{
    private const string POSTGRESQL_VERSION = '16';

    private const string ENV_VAR_TEMPLATE = "DATABASE_URL=postgresql://test:test@{{host}}:{{port}}/test?serverVersion=16&charset=utf8";

    protected function getEnvVarTemplate(): string
    {
        return self::ENV_VAR_TEMPLATE;
    }

    protected function createContainer(): GenericContainer
    {
        return new PostgresContainer(self::POSTGRESQL_VERSION)
            ->withWait(new WaitForLog('ready to accept connections'))
        ;
    }
    /*
    public function start(): void
    {
        if (!$this->container instanceof StartedGenericContainer) {
            $container = new PostgresContainer(self::POSTGRESQL_VERSION)
                ->withWait(new WaitForLog('ready to accept connections'))
            ;
            $this->container = $container->start();
            $host = isset($_ENV['DOCKER_HOST_OS_FAMILY']) && $_ENV['DOCKER_HOST_OS_FAMILY'] != 'linux' ? 'host.docker.internal' : $this->container->getHost();
            putenv("DATABASE_URL=postgresql://test:test@$host:{$this->container->getFirstMappedPort()}/test?serverVersion=16&charset=utf8");
        }
    }*/
}
