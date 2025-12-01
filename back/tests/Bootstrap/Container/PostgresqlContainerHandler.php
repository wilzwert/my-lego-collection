<?php

namespace App\Tests\Bootstrap\Container;

use App\Tests\Bootstrap\Container\AbstractTestContainerHandler;
use Testcontainers\Container\GenericContainer;
use Testcontainers\Modules\PostgresContainer;
use Testcontainers\Wait\WaitForLog;

/**
 * @author Wilhelm Zwertvaegher
 */
final class PostgresqlContainerHandler extends AbstractTestContainerHandler
{
    private const string POSTGRESQL_VERSION = '16';

    private const string ENV_VAR_TEMPLATE = 'DATABASE_URL=postgresql://test:test@{{host}}:{{port}}/test?serverVersion=16&charset=utf8';

    protected function getEnvVarTemplate(): string
    {
        return self::ENV_VAR_TEMPLATE;
    }

    protected function createContainer(): GenericContainer
    {
        return new PostgresContainer(self::POSTGRESQL_VERSION)
            ->withWait(new WaitForLog('ready to accept connections', false, 60000))
        ;
    }
}
