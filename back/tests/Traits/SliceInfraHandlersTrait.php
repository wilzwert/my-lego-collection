<?php

namespace App\Tests\Traits;

use App\Shared\Infrastructure\EventHandler\MessageHandler;
use MyLegoCollection\SharedEvent\Message;
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
     * @template T of Message
     * @param string $slice
     * @param list<class-string<T>> $eventClasses
     * @return void
     */
    protected function assertHasMessageHandlers(string $slice, array $eventClasses): void
    {
        $handledEventClasses = $this->getSliceHandledMessages($slice);
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
     * @template T of Message
     * @param string $slice
     * @param list<class-string<T>> $messageClasses
     * @return bool
     */
    protected function sliceHasHandlers(string $slice, array $messageClasses): bool
    {
        if (empty($messageClasses) || count($messageClasses) === 0) {
            return true;
        }

        return $this->getSliceHandledMessages($slice) === $messageClasses;
    }

    /**
     * @param string $slice
     * @return list<class-string>
     */
    protected function getSliceHandledMessages(string $slice): array
    {
        return array_map(fn ($handler) => $handler::getMessageHandled(), $this->getSliceInfraHandlers($slice));
    }

    /**
     * Returns all classes implementing DomainEventHandler or IntegrationEventHandler in the given slice
     */
    protected function getSliceInfraHandlers(string $slice): array
    {
        $directory = $this->getSliceHandlerBasePath($slice);
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
                    if ($ref->implementsInterface(MessageHandler::class)) {
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
    protected function getSliceHandlerBasePath(string $slice): string
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
