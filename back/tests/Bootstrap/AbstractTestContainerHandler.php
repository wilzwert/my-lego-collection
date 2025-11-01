<?php

namespace App\Tests\Bootstrap;

use Testcontainers\Container\GenericContainer;
use Testcontainers\Container\StartedGenericContainer;
use Testcontainers\Modules\PostgresContainer;
use Testcontainers\Wait\WaitForLog;

/**
 * @author Wilhelm Zwertvaegher
 */
abstract class AbstractTestContainerHandler implements TestContainerHandler
{
    private ?StartedGenericContainer $container = null;

    #[\Override]
    public function getHost(): string
    {
        if (!$this->container) {
            throw new \Exception('Host cannot be determined before the container is started.');
        }
        return isset($_ENV['DOCKER_HOST_OS_FAMILY']) && $_ENV['DOCKER_HOST_OS_FAMILY'] != 'linux' ? 'host.docker.internal' : $this->container->getHost();
    }

    public function start(): void
    {
        if (!$this->container instanceof StartedGenericContainer) {
            $container = $this->createContainer();
            $this->container = $container->start();
            $envVar = str_replace(
                ['{{host}}', '{{port}}'],
                [$this->getHost(), $this->container->getFirstMappedPort()],
                $this->getEnvVarTemplate()
            );
            putenv($envVar);
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

    abstract protected function getEnvVarTemplate() :string;

    abstract protected function createContainer(): GenericContainer;
}
