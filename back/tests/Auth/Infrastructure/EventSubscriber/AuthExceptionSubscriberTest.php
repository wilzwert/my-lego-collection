<?php

namespace App\Tests\Auth\Infrastructure\EventSubscriber;

use App\Auth\Domain\Exception\IdentityAlreadyExistsException;
use App\Auth\Infrastructure\EventSubscriber\AuthExceptionSubscriber;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Wilhelm Zwertvaegher
 */

final class AuthExceptionSubscriberTest extends TestCase
{
    #[Test]
    public function shouldThrowHttpException_whenUserAlreadyExists(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $exception = new IdentityAlreadyExistsException('Identity already exists');
        $event = new ExceptionEvent(
            $kernel,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $subscriber = new AuthExceptionSubscriber();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Identity already exists');

        try {
            $subscriber->onExceptionEvent($event);
        } catch (HttpException $e) {
            $this->assertSame(Response::HTTP_CONFLICT, $e->getStatusCode());
            $this->assertSame($exception, $e->getPrevious());
            throw $e;
        }
    }

    #[Test]
    public function shouldDoNothing_forUnsupportedExceptions(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $exception = new \RuntimeException('Unexpected error');
        $event = new ExceptionEvent(
            $kernel,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $this->expectNotToPerformAssertions();

        $subscriber = new AuthExceptionSubscriber();

        // this should throw no exception
        $subscriber->onExceptionEvent($event);
    }

    #[Test]
    public function shouldSubscribeToExceptionEvent(): void
    {
        $events = AuthExceptionSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(ExceptionEvent::class, $events);
        $this->assertSame('onExceptionEvent', $events[ExceptionEvent::class]);
    }
}
