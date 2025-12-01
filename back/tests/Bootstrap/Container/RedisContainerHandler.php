<?php

namespace App\Tests\Bootstrap\Container;

use Testcontainers\Container\GenericContainer;
use Testcontainers\Modules\RedisContainer;
use Testcontainers\Wait\WaitForLog;

/**
 * @author Wilhelm Zwertvaegher
 */
final class RedisContainerHandler extends AbstractTestContainerHandler
{
    private const string REDIS_VERSION = '7';

    private const string ENV_VAR_TEMPLATE = 'REDIS_URL=redis://{{host}}:{{port}}';

    protected function getEnvVarTemplate(): string
    {
        return self::ENV_VAR_TEMPLATE;
    }

    protected function createContainer(): GenericContainer
    {
        return new RedisContainer(self::REDIS_VERSION)
            ->withWait(new WaitForLog('Ready to accept connections', false, 60000));
    }
}
