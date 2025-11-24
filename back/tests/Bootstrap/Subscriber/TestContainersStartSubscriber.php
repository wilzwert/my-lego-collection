<?php

namespace App\Tests\Bootstrap\Subscriber;

use App\Tests\Bootstrap\Container\TestContainerHandler;
use App\Tests\Bootstrap\TestSuiteService;
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

    public function notify(ExecutionStarted $event): void
    {
        if ($this->suiteService->isIntegrationTest($event->testSuite())) {
            $envVars = [];
            foreach ($this->containerHandlers as $handler) {
                fwrite(STDOUT, "Starting ".$handler::class. PHP_EOL);
                $handler->start();
                $envVars = array_merge($envVars, $handler->getEnvVars());
            }
            // set env and generate a temporary env file and force symfony reload env and use our generated env vars
            foreach ($envVars as $envVar) {
                putenv($envVar);
            }
            $envFile = '.env.test.local';
            $this->fs->dumpFile($envFile, implode("\n", $envVars));
            $dotenv = new Dotenv();
            $dotenv->overload($envFile);
        }
    }
}
