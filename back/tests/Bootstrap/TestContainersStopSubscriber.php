<?php

namespace App\Tests\Bootstrap;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;
use Symfony\Component\Filesystem\Filesystem;

/**
 *  Stop TestContainers if needed.
 *
 * @author Wilhelm Zwertvaegher
 */
readonly class TestContainersStopSubscriber implements ExecutionFinishedSubscriber
{
    public function __construct(
        private TestSuiteService $suiteService,
        private TestContainerHandler $dbTestContainerHandler,
        private TestContainerHandler $redisTestContainerHandler,
        private Filesystem $fs = new Filesystem(),
    ) {
    }

    public function notify(ExecutionFinished $event): void
    {
        if ($this->suiteService->isIntegrationTest()) {
            $this->redisTestContainerHandler->stop();

            $this->dbTestContainerHandler->stop();

            // cleanup temporary generated env file
            $this->fs->remove('.env.test.local');
        }
    }
}
