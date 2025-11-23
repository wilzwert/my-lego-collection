<?php

namespace App\Tests\Bootstrap\Subscriber;

use App\Tests\Bootstrap\Container\TestContainerHandler;
use App\Tests\Bootstrap\TestSuiteService;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use Symfony\Component\Process\Process;

/**
 * Loads fixtures on tests execution start if integration tests are detected.
 *
 * @author Wilhelm Zwertvaegher
 */
readonly class DoctrineFixturesSubscriber implements ExecutionStartedSubscriber
{
    public function __construct(
        private TestSuiteService $suiteService,
        private TestContainerHandler $dbContainerHandler,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function notify(ExecutionStarted $event): void
    {
        if (!$this->suiteService->isIntegrationTest($event->testSuite())) {
            return;
        }

        if (!$this->dbContainerHandler->isStarted()) {
            throw new \Exception('Db container MUST be started before loading test fixtures.');
        }


        $env = [
            'DATABASE_URL' => getenv('DATABASE_URL'),
        ];

        try {
            fwrite(STDOUT, 'Creating database' . PHP_EOL);
            $this->runSymfonyCommand('doctrine:database:create --env=test --if-not-exists', $env);
            fwrite(STDOUT, 'Creating schema' . PHP_EOL);
            $this->runSymfonyCommand('doctrine:schema:create --env=test --no-interaction', $env);
            fwrite(STDOUT, 'Loading fixtures' . PHP_EOL);
            $this->runSymfonyCommand('doctrine:fixtures:load --env=test --no-interaction', $env);
        }
        catch (\Exception $e) {
            fwrite(STDERR, $e->getMessage() . PHP_EOL);
            throw $e;
        }
    }

    /**
     * @param array<string, string> $env
     */
    private function runSymfonyCommand(string $cmd, array $env): void
    {
        $process = Process::fromShellCommandline("php bin/console {$cmd}", null, $env);
        $process->mustRun();
        if(!$process->isSuccessful()) {
            throw new \Exception('Failed to execute command: ' . $process->getErrorOutput());
        }
    }
}
