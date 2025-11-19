<?php

namespace App\Tests;

use App\Tests\Bootstrap\DoctrineFixturesSubscriber;
use App\Tests\Bootstrap\PostgresqlContainerHandler;
use App\Tests\Bootstrap\RedisContainerHandler;
use App\Tests\Bootstrap\TestContainersStartSubscriber;
use App\Tests\Bootstrap\TestContainersStopSubscriber;
use App\Tests\Bootstrap\TestSuiteService;
use App\Tests\Bootstrap\TmpUploadsStopSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Wilhelm Zwertvaegher
 */
final class IntegrationTestExtension implements Extension
{

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $testSuiteService = new TestSuiteService();
        $dbContainerHandler = new PostgresqlContainerHandler();
        $cacheContainerHandler = new RedisContainerHandler();
        $fs = new Filesystem();

        $facade->registerSubscriber(new TestContainersStartSubscriber($testSuiteService, $dbContainerHandler, $cacheContainerHandler, $fs));
        $facade->registerSubscriber(new DoctrineFixturesSubscriber($testSuiteService, $dbContainerHandler));
        $facade->registerSubscriber(new TestContainersStopSubscriber($testSuiteService, $dbContainerHandler, $cacheContainerHandler, $fs));
        $facade->registerSubscriber(new TmpUploadsStopSubscriber($testSuiteService, $fs));
    }
}
