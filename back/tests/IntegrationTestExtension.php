<?php

namespace App\Tests;

use App\Tests\Bootstrap\Container\RabbitMqContainerHandler;
use App\Tests\Bootstrap\Subscriber\DoctrineFixturesSubscriber;
use App\Tests\Bootstrap\Container\PostgresqlContainerHandler;
use App\Tests\Bootstrap\Container\RedisContainerHandler;
use App\Tests\Bootstrap\Subscriber\TestContainersStartSubscriber;
use App\Tests\Bootstrap\Subscriber\TestContainersStopSubscriber;
use App\Tests\Bootstrap\TestSuiteService;
use App\Tests\Bootstrap\Subscriber\TmpUploadsStopSubscriber;
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
        $rabbitMQContainerHandler = new RabbitMQContainerHandler();
        $cacheContainerHandler = new RedisContainerHandler();
        $containerHandlers = [$dbContainerHandler, $cacheContainerHandler, $rabbitMQContainerHandler];

        $fs = new Filesystem();

        $facade->registerSubscriber(new TestContainersStartSubscriber($testSuiteService, $containerHandlers, $fs));
        $facade->registerSubscriber(new DoctrineFixturesSubscriber($testSuiteService, $dbContainerHandler));
        $facade->registerSubscriber(new TestContainersStopSubscriber($testSuiteService, $containerHandlers, $fs));
        $facade->registerSubscriber(new TmpUploadsStopSubscriber($testSuiteService, $fs));
    }
}
