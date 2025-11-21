<?php

namespace App\Tests\Bootstrap;

use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Start TestContainers if needed.
 *
 * @author Wilhelm Zwertvaegher
 */
readonly class TestContainersStartSubscriber implements ExecutionStartedSubscriber
{
    public function __construct(
        private TestSuiteService $suiteService,
        private TestContainerHandler $dbTestContainerHandler,
        private TestContainerHandler $redisTestContainerHandler,
        private Filesystem $fs = new Filesystem(),
    ) {
    }

    public function notify(ExecutionStarted $event): void
    {
        if ($this->suiteService->isIntegrationTest($event->testSuite())) {
            $this->dbTestContainerHandler->start();
            $envVars = $this->dbTestContainerHandler->getEnvVars();

            $this->redisTestContainerHandler->start();

            $envVars = array_merge($envVars, $this->redisTestContainerHandler->getEnvVars());

            // generate a temporary env file and force symfony reload env and use our generated env vars
            $envFile = '.env.test.local';
            $this->fs->dumpFile($envFile, implode("\n", $envVars));
            $dotenv = new Dotenv();
            $dotenv->overload($envFile);
        }
    }
}
