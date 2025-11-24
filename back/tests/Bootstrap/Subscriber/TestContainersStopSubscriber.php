<?php

namespace App\Tests\Bootstrap\Subscriber;

use App\Tests\Bootstrap\Container\TestContainerHandler;
use App\Tests\Bootstrap\TestSuiteService;
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
    /**
     * @param TestSuiteService $suiteService
     * @param array<TestContainerHandler> $containerHandlers
     * @param Filesystem $fs
     */
    public function __construct(
        private TestSuiteService $suiteService,
        private array $containerHandlers,
        private Filesystem $fs = new Filesystem(),
    ) {
    }

    public function notify(ExecutionFinished $event): void
    {
        if ($this->suiteService->isIntegrationTest()) {
            foreach (array_reverse($this->containerHandlers) as $containerHandler) {
                fwrite(STDOUT, "Stopping ".$containerHandler::class . PHP_EOL);
                $containerHandler->stop();
            }

            // cleanup temporary generated env file
            fwrite(STDOUT, "Cleaning local test env file" . PHP_EOL);
            $this->fs->remove('.env.test.local');
        }
    }
}
