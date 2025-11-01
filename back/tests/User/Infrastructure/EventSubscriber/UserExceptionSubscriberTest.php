<?php

namespace App\Tests\User\Infrastructure\EventSubscriber;

use App\User\Domain\Exception\UserAlreadyExistsException;
use App\User\Infrastructure\EventSubscriber\UserExceptionSubscriber;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Wilhelm Zwertvaegher
 */

final class UserExceptionSubscriberTest extends TestCase
{
    #[Test]
    public function shouldThrowHttpException_whenUserAlreadyExists(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $exception = new UserAlreadyExistsException('User already exists');
        $event = new ExceptionEvent(
            $kernel,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $subscriber = new UserExceptionSubscriber();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('User already exists');

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

        $subscriber = new UserExceptionSubscriber();

        // this should throw no exception
        $subscriber->onExceptionEvent($event);
    }

    #[Test]
    public function shouldSubscribeToExceptionEvent(): void
    {
        $events = UserExceptionSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(ExceptionEvent::class, $events);
        $this->assertSame('onExceptionEvent', $events[ExceptionEvent::class]);
    }
}
