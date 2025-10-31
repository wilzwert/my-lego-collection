<?php

namespace App\Tests\Bootstrap;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;

/**
 * @author Wilhelm Zwertvaegher
 */
class TestContainersStopSubscriber implements ExecutionFinishedSubscriber
{

    public function __construct(
        private readonly TestSuiteService $suiteService,
        private readonly TestContainerHandler $dbTestContainerHandler,
        private readonly TestContainerHandler $redisTestContainerHandler
    ) {
    }

    public function notify(ExecutionFinished $event): void
    {
        if ($this->suiteService->isIntegrationTest()) {
            echo "STOPPING REDIS CONTAINER....\n";
            $this->redisTestContainerHandler->stop();

            echo "STOPPING DB CONTAINER....\n";
            $this->dbTestContainerHandler->stop();

        }
    }
}
