<?php

namespace App\Tests\Bootstrap;

use PHPUnit\Event\TestSuite\TestSuite;

/**
 * @author Wilhelm Zwertvaegher
 */
final class TestSuiteService
{
    private ?bool $isTestSuite = null;

    public function isIntegrationTest(?TestSuite $testSuite = null): bool
    {
        if (null === $this->isTestSuite) {
            $this->isTestSuite = null !== $testSuite && array_any($testSuite->tests()->asArray(), fn ($test) => str_ends_with($test->file(), 'IT.php'));
        }
        return $this->isTestSuite;
    }
}
