<?php

namespace App\Tests\Bootstrap;

use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Wilhelm Zwertvaegher
 */
class TestContainersStartSubscriber implements ExecutionStartedSubscriber
{

    public function __construct(
        private readonly TestSuiteService $suiteService,
        private readonly TestContainerHandler $dbTestContainerHandler,
        private readonly TestContainerHandler $redisTestContainerHandler,
        private Filesystem $fs
    ) {
    }

    public function notify(ExecutionStarted $event): void
    {
        if ($this->suiteService->isIntegrationTest($event->testSuite())) {
            fwrite(STDOUT, "STARTING DB CONTAINER....\n");
            $this->dbTestContainerHandler->start();
            $envVars = $this->dbTestContainerHandler->getEnvVars();

            fwrite(STDOUT, "STARTING REDIS CONTAINER....\n");
            $this->redisTestContainerHandler->start();

            $envVars = array_merge($envVars, $this->redisTestContainerHandler->getEnvVars());

            $envFile = '.env.test.local';
            $this->fs->dumpFile($envFile, implode("\n", $envVars));

            // reload env to force Symfony to use our new env vars
            $dotenv = new Dotenv();
            $dotenv->overload($envFile);
        }
    }
}
