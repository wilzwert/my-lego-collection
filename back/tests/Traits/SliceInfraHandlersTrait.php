<?php

namespace App\Tests\Traits;

use App\Shared\Infrastructure\EventHandler\IntegrationEventHandler;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

/**
 * @author Wilhelm Zwertvaegher
 */
trait SliceInfraHandlersTrait
{

    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * @param string $slice
     * @param list<class-string> $eventClasses
     * @return void
     */
    protected function assertHasDomainEventHandlers(string $slice, array $eventClasses): void
    {
        $handledEventClasses = $this->getSliceHandledEvents($slice);
        $missing = [];
        foreach ($eventClasses as $eventClass) {
            if (!in_array($eventClass, $handledEventClasses)) {
                $missing[] = $eventClass;
            }
        }

        if (count($missing) > 0) {
            $this->fail(sprintf('Missing events handlers %s for slice %s', implode(', ', $missing), $slice));
        }
        $this->expectNotToPerformAssertions();
    }

    /**
     * @param string $slice
     * @param list<class-string> $eventClasses
     * @return bool
     */
    protected function sliceHasHandlers(string $slice, array $eventClasses): bool
    {
        if (empty($eventClasses) || count($eventClasses) === 0) {
            return true;
        }

        return $this->getSliceHandledEvents($slice) === $eventClasses;
    }

    /**
     * @param string $slice
     * @return list<class-string>
     */
    protected function getSliceHandledEvents(string $slice): array
    {
        return array_map(fn ($handler) => $handler::getEventHandled(), $this->getSliceInfraHandlers($slice));
    }

    /**
     * Returns all classes implementing DomainEventHandler in the given slice
     */
    protected function getSliceInfraHandlers(string $slice): array
    {
        $directory = $this->getSliceEventHandlerPath($slice);
        $classes = [];

        if (!is_dir($directory)) {
            return $classes;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
                $class = $this->classFromFile($file->getRealPath());
                if ($class && class_exists($class)) {
                    $ref = new ReflectionClass($class);
                    if ($ref->implementsInterface(IntegrationEventHandler::class)) {
                        $classes[] = $class;
                    }
                }
            }
        }

        return $classes;
    }

    /**
     * Returns an absolute path to the EventHandler adapters path for the given slice
     */
    protected function getSliceEventHandlerPath(string $slice): string
    {
        return self::$kernel->getProjectDir() . '/src/' . $slice . '/Infrastructure/EventHandler';
    }

    /**
     * Builds an FQCN from the file's absolute path
     */
    protected function classFromFile(string $filePath): ?string
    {
        // Assuming src/ is the root of the App namespace
        $relative = str_replace(self::$kernel->getProjectDir() . '/src/', '', $filePath);
        return 'App\\' . str_replace(['/', '.php'], ['\\', ''], $relative);
    }
}
