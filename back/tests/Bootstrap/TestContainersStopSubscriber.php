<?php

namespace App\Tests\Bootstrap;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Wilhelm Zwertvaegher
 */
class TestContainersStopSubscriber implements ExecutionFinishedSubscriber
{

    public function __construct(
        private readonly TestSuiteService $suiteService,
        private readonly TestContainerHandler $dbTestContainerHandler,
        private readonly TestContainerHandler $redisTestContainerHandler,
        private Filesystem $fs
    ) {
    }

    public function notify(ExecutionFinished $event): void
    {
        if ($this->suiteService->isIntegrationTest()) {
            echo "STOPPING REDIS CONTAINER....\n";
            $this->redisTestContainerHandler->stop();

            echo "STOPPING DB CONTAINER....\n";
            $this->dbTestContainerHandler->stop();

            $this->fs->remove('.env.test.local');

        }
    }
}
