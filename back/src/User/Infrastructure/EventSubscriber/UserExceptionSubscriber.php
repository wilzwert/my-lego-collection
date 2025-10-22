<?php

namespace App\User\Infrastructure\EventSubscriber;

use App\User\Domain\Exception\UserAlreadyExistsException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserExceptionSubscriber implements EventSubscriberInterface
{

    private static array $supportedExceptionTypes = [
        UserAlreadyExistsException::class => Response::HTTP_CONFLICT
    ];

    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $httpStatusCode =  self::$supportedExceptionTypes[get_class($event->getThrowable())] ?? null;
        // wrap the domain exception into an HttpException with the appropriate status code
        if($httpStatusCode) {
            throw new HttpException(
                $httpStatusCode,
                $event->getThrowable()->getMessage(),
                $event->getThrowable()
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [ExceptionEvent::class => 'onExceptionEvent'];
    }
}
