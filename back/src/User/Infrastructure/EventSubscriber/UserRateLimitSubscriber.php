<?php

namespace App\User\Infrastructure\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

class UserRateLimitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security                    $security,
        private readonly RateLimiterFactoryInterface $registerByIpLimiter,
        private readonly RateLimiterFactoryInterface $publicApiByIpLimiter,
        private readonly RateLimiterFactoryInterface $apiByUserLimiter
    ) {

    }

    public function onKernelController(ControllerEvent $event): void
    {
        if ($event->getRequestType() !== HttpKernelInterface::MAIN_REQUEST) {
            return;
        }

        $request = $event->getRequest();
        $user = $this->security->getUser();

        $routesToLimit = [
            'api_user_register',
            'api_sets_search',
        ];
        $route = $request->attributes->get('_route');
        if (!in_array($route, $routesToLimit, true)) {
            return;
        }

        if ($route === 'api_user_register') {
            $factory = $this->registerByIpLimiter;
            $limiter = $factory->create($request->getClientIp());
        } else {
            $factory = ($user ? $this->apiByUserLimiter : $this->publicApiByIpLimiter);
            $limiter = $factory->create($user ? $user->getUserIdentifier() : $request->getClientIp());
        }

        if (false === $limiter->consume()->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
