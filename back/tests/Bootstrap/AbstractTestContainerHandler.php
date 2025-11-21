<?php

namespace App\Tests\Bootstrap;

use Testcontainers\Container\GenericContainer;
use Testcontainers\Container\StartedGenericContainer;

/**
 * TestContainer handler for starting, stopping a container, and generating env vars if necessary.
 *
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

        return getenv('TESTCONTAINERS_HOST') ?: 'host.docker.internal';
    }

    /**
     * @return list<string>
     *
     * @throws \Exception
     */
    public function getEnvVars(): array
    {
        return [str_replace(
            ['{{host}}', '{{port}}'],
            [$this->getHost(), (string) $this->container->getFirstMappedPort()],
            $this->getEnvVarTemplate()
        )];
    }

    /**
     * @throws \Exception
     */
    public function start(): void
    {
        if (!$this->container instanceof StartedGenericContainer) {
            fwrite(STDOUT, 'Starting '.get_class($this).PHP_EOL);

            $container = $this->createContainer();
            $this->container = $container->start();
            foreach ($this->getEnvVars() as $envVar) {
                putenv($envVar);
            }
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

    abstract protected function getEnvVarTemplate(): string;

    abstract protected function createContainer(): GenericContainer;
}
