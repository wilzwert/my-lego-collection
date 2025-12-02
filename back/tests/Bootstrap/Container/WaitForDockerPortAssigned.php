<?php

namespace App\Tests\Bootstrap\Container;

use Docker\API\Model\ContainersIdJsonGetResponse200;
use Testcontainers\Container\StartedTestContainer;
use Testcontainers\Wait\WaitStrategy;

/**
 * @author Wilhelm Zwertvaegher
 */
class WaitForDockerPortAssigned implements WaitStrategy
{
    public function wait(StartedTestContainer $container): void
    {
        $timeout = 30;
        $start = time();

        while (true) {
            $id = $container->getId();
            fwrite(STDERR, "Container ID: $id\n");

            passthru("docker info | grep -i rootless");
            /** @var ContainersIdJsonGetResponse200 $inspect */
            $inspect = $container->getClient()->containerInspect($id);
            $ports = $inspect->getNetworkSettings()->getPorts();

            if (!empty($ports['5672/tcp'][0]->getHostPort())) {
                fwrite(STDERR, 'Port available  '.$ports['5672/tcp'][0]->getHostPort(). PHP_EOL);
                return;
            }

            fwrite(STDERR, 'No port available yet '.PHP_EOL);

            if (time() - $start > $timeout) {
                throw new \RuntimeException("Port not assigned after $timeout seconds");
            }

            usleep(200_000);
        }
    }
}
