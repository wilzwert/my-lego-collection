<?php

namespace App\Tests\Traits;

use Symfony\Component\HttpKernel\KernelInterface;

trait TestResourcesTrait
{
    protected function getTestResourcePath(string $relativePath): string
    {
        if (method_exists($this, 'bootKernel')) {
            $kernel = self::bootKernel();
            $baseDir = $kernel->getProjectDir();
        } elseif (property_exists($this, 'kernel') && $this->kernel instanceof KernelInterface) {
            $baseDir = $this->kernel->getProjectDir();
        } else {
            // fallback for pure unit tests
            $baseDir = dirname(__DIR__, 1);
        }

        return sprintf('%s/tests/Resources/%s', $baseDir, ltrim($relativePath, '/'));
    }
}

