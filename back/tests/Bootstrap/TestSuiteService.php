<?php

namespace App\Tests\Bootstrap;

use PHPUnit\Event\Code\Test;
use PHPUnit\Event\TestSuite\TestSuite;

/**
 * Utility service for tests suites.
 *
 * @see TestSuite
 *
 * @author Wilhelm Zwertvaegher
 */
final class TestSuiteService
{
    private ?bool $isTestSuite = null;

    private function isIntegration(Test $test): bool
    {
        return str_ends_with($test->file(), 'IT.php')
            || str_contains($test->file(), '/EndToEnd/');
    }

    /**
     * Detects integration tests in a suite
     * Integration tests filenames MUST end with IT.php to be detected.
     */
    public function isIntegrationTest(?TestSuite $testSuite = null): bool
    {
        if (null === $this->isTestSuite) {
            $this->isTestSuite = null !== $testSuite
                && array_any(
                    $testSuite->tests()->asArray(),
                    fn (Test $test) => $this->isIntegration($test)
                );
        }

        return $this->isTestSuite;
    }
}
