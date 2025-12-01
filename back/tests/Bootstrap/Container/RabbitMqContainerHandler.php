<?php

namespace App\Tests\Bootstrap\Container;

use App\Tests\Bootstrap\Container\AbstractTestContainerHandler;
use Testcontainers\Container\GenericContainer;
use Testcontainers\Modules\RedisContainer;
use Testcontainers\Wait\WaitForHostPort;
use Testcontainers\Wait\WaitForLog;

/**
 * @author Wilhelm Zwertvaegher
 */
final class RabbitMqContainerHandler extends AbstractTestContainerHandler
{
    private const string RABBITMQ_VERSION = 'rabbitmq:4';

    private const string ENV_VAR_TEMPLATE = 'MESSENGER_TRANSPORT_DSN=amqp://test:test@{{host}}:{{port}}/%2f/messages';

    protected function getEnvVarTemplate(): string
    {
        return self::ENV_VAR_TEMPLATE;
    }

    protected function createContainer(): GenericContainer
    {
        return new GenericContainer(self::RABBITMQ_VERSION)
            ->withExposedPorts(5672)  // port AMQP + port management
            ->withEnvironment(['RABBITMQ_DEFAULT_USER' => 'test', 'RABBITMQ_DEFAULT_PASS' => 'test'])
            // ->withWait(new WaitForLog('Server startup complete', false, 30000))
            ->withWait(new WaitForHostPort(30000))
        ;
    }
}
