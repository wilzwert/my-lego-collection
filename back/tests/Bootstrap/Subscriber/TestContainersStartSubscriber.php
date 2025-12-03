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
class TestContainersStartSubscriber implements ExecutionStartedSubscriber
{
    private bool $containersStarted = false;

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
        fwrite(STDOUT, 'calling notify on TestContainersStartSubscriber'.PHP_EOL);
        if ($this->suiteService->isIntegrationTest($event->testSuite()) && !$this->containersStarted) {
            $this->containersStarted = true;
            $envVars = [];
            $container = null;
            foreach ($this->containerHandlers as $handler) {
                try {
                    fwrite(STDOUT, "Starting " . $handler::class . PHP_EOL);
                    $container = $handler->start();
                    fwrite(STDOUT, "Started " . $handler::class . PHP_EOL);
                    $envVars = array_merge($envVars, $handler->getEnvVars());
                    fwrite(STDOUT, "Collected env from " . $handler::class . PHP_EOL);
                } catch (\Throwable $e) {
                    fwrite(STDERR, "Failed to start " . $handler::class . ' : '.$e->getMessage(). PHP_EOL);
                    if (isset($container)) {
                        $containerId = $container->getId();
                        fwrite(STDOUT, "Container logs:\n");
                    }
                    exit(1);
                }
            }

            fwrite(STDOUT, "All handlers started, updating env\n");

            // set env and generate a temporary env file and force symfony reload env and use our generated env vars
            foreach ($envVars as $envVar) {
                putenv($envVar);
            }

            fwrite(STDOUT, "Writing to .env.test.local ".implode("\n", $envVars).PHP_EOL);
            $envFile = '.env.test.local';
            $this->fs->dumpFile($envFile, implode("\n", $envVars));
            $dotenv = new Dotenv();
            $dotenv->overload($envFile);
            fwrite(STDOUT, "Env file written\n");
        }
    }
}
