<?php

namespace App\Tests\Bootstrap;

use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;

/**
 * @author Wilhelm Zwertvaegher
 */
class TestContainersStartSubscriber implements ExecutionStartedSubscriber
{

    public function __construct(
        private readonly TestSuiteService $suiteService,
        private readonly TestContainerHandler $dbTestContainerHandler,
        private readonly TestContainerHandler $redisTestContainerHandler
    ) {
    }

    public function notify(ExecutionStarted $event): void
    {
        if ($this->suiteService->isIntegrationTest($event->testSuite())) {
            echo "STARTING DB CONTAINER....\n";
            $this->dbTestContainerHandler->start();

            echo "STARTING REDIS CONTAINER....\n";
            $this->redisTestContainerHandler->start();
        }
    }
}
