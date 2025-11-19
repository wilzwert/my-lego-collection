<?php

namespace App\Tests\Bootstrap;

use Exception;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;
use Symfony\Component\Process\Process;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class DoctrineFixturesSubscriber implements ExecutionStartedSubscriber
{

    public function __construct(
        private TestSuiteService $suiteService,
        private TestContainerHandler $dbContainerHandler
    ) {
    }

    /**
     * @throws Exception
     */
    public function notify(ExecutionStarted $event): void
    {
        if (!$this->suiteService->isIntegrationTest($event->testSuite())) {
            return;
        }

        if (!$this->dbContainerHandler->isStarted()) {
            throw new Exception('Db container MUST be started before loading test fixtures.');
        }
        fwrite(STDOUT, "LOADING FIXTURES....\n");
        $env = [
            'DATABASE_URL' => getenv('DATABASE_URL')
        ];

        $this->runSymfonyCommand('doctrine:database:create --env=test --if-not-exists', $env);
        $this->runSymfonyCommand('doctrine:schema:create --env=test --no-interaction', $env);
        $this->runSymfonyCommand('doctrine:fixtures:load --env=test --no-interaction', $env);
    }

    private function runSymfonyCommand(string $cmd, array $env): void
    {
        $process = Process::fromShellCommandline("php bin/console {$cmd}", null, $env);
        $process->mustRun();
    }
}
