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

    private const int RABBITMQ_PORT = 5672;

    private const string ENV_VAR_TEMPLATE = 'MESSENGER_TRANSPORT_DSN=amqp://test:test@{{host}}:{{port}}/%2f/messages';

    protected function getEnvVarTemplate(): string
    {
        return self::ENV_VAR_TEMPLATE;
    }

    #[\Override]
    public function getFirstMappedPort(): int
    {
        // for some unknown reason, in CI (GitHub action), getFirstMappedPort does not work
        // we have to freshly inspect the container to get the actual host port

        $inspect = $this->container->getClient()->containerInspect($this->container->getId());
        $ports = $inspect->getNetworkSettings()->getPorts();
        $lookupPort = sprintf('%s/tcp', self::RABBITMQ_PORT);
        if (!empty($ports[$lookupPort]) &&!empty($ports[$lookupPort][0]) && !empty($ports[$lookupPort][0]->getHostPort())) {
            return (int)$ports[$lookupPort][0]->getHostPort();
        }

        return parent::getFirstMappedPort();
    }

    protected function createContainer(): GenericContainer
    {
        return new GenericContainer(self::RABBITMQ_VERSION)
            ->withExposedPorts(self::RABBITMQ_PORT)  // port AMQP + port management
            ->withEnvironment(['RABBITMQ_DEFAULT_USER' => 'test', 'RABBITMQ_DEFAULT_PASS' => 'test'])
            // for some unknown reason, in CI (GitHub action), WaitForLog + getFirstMappedPort does not work
            // so we had to create a custom strategy to freshly inspect the container to get the actual host port
            ->withWait(
                new WaitForDockerPortAssigned(
                    self::RABBITMQ_PORT,
                    30000
                )->withWait(new WaitForLog('Server startup complete', false, 30000))
            )
            // ->withWait(new WaitForLog('Server startup complete', false, 30000))
            // ->withWait(new WaitForHostPort(30000))
        ;
    }
}
