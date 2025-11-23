<?php

namespace App\Tests\Bootstrap\Subscriber;

use App\Tests\Bootstrap\TestSuiteService;
use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Wilhelm Zwertvaegher
 */
class TmpUploadsStopSubscriber implements ExecutionFinishedSubscriber
{
    public function __construct(
        private readonly TestSuiteService $suiteService,
        private readonly Filesystem $fs
    ) {
    }

    public function notify(ExecutionFinished $event): void
    {
        if ($this->suiteService->isIntegrationTest()) {
            $this->fs->remove(__DIR__ . '/../../var/test/uploads');
        }
    }
}
