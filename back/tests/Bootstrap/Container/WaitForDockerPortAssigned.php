<?php

namespace App\Tests\Bootstrap\Container;

use Docker\API\Model\ContainersIdJsonGetResponse200;
use Testcontainers\Container\StartedTestContainer;
use Testcontainers\Exception\ContainerNotReadyException;
use Testcontainers\Wait\BaseWaitStrategy;
use Testcontainers\Wait\WaitStrategy;

/**
 * @author Wilhelm Zwertvaegher
 */
class WaitForDockerPortAssigned extends BaseWaitStrategy
{

    private ?WaitStrategy $decorated = null;

    public function __construct(private readonly int $port, int $timeout = 10000, int $pollInterval = 500)
    {
        parent::__construct($timeout, $pollInterval);
    }

    public function withWait(WaitStrategy $wait): self
    {
        $this->decorated = $wait;
        return $this;
    }

    public function wait(StartedTestContainer $container): void
    {
        $id = $container->getId();
        $lookupPort = "{$this->port}/tcp";
        $startTime = microtime(true) * 1000;

        while (true) {
            $elapsedTime = (microtime(true) * 1000) - $startTime;
            if ($elapsedTime > $this->timeout) {
                throw new ContainerNotReadyException($id);
            }

            /** @var ContainersIdJsonGetResponse200 $inspect */
            $inspect = $container->getClient()->containerInspect($id);
            $ports = $inspect->getNetworkSettings()->getPorts();

            if (!empty($ports[$lookupPort]) && !empty($ports[$lookupPort][0]) && !empty($ports[$lookupPort][0]->getHostPort())) {
                if ($this->decorated) {
                    $this->decorated->wait($container);
                }
                return;
            }

            usleep($this->pollInterval * 1000);
        }
    }
}
