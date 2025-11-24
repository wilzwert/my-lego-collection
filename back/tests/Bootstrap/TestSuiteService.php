<?php

namespace App\Tests\Bootstrap;

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

    /**
     * Detects integration tests in a suite
     * Integration tests filenames MUST end with IT.php to be detected.
     */
    public function isIntegrationTest(?TestSuite $testSuite = null): bool
    {
        if (null === $this->isTestSuite) {
            $this->isTestSuite = null !== $testSuite && array_any($testSuite->tests()->asArray(), fn ($test) => str_ends_with($test->file(), 'IT.php'));
        }

        return $this->isTestSuite;
    }
}
