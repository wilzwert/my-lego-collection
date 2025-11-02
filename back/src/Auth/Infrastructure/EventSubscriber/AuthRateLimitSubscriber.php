<?php

namespace App\Auth\Infrastructure\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;

/**
 * This rate limit applies to the Identity 'slice' only
 * Other slices may have their own subscribers (or not)
 * TODO : maybe we should consider the rate limit a global feature instead or relying on each slice to implement it
 * Although it may seem 'cleaner' to have each slice implement it, it may result in code duplication
 * Moreover, should slices become isolated micro services, the rate limiting would probably be handled differently
 * We should make a decision, but it's not that important at the moment
 *
 * @author Wilhelm Zwertvaegher
 *
 */
readonly class AuthRateLimitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security                    $security,
        private RateLimiterFactoryInterface $registerByIpLimiter,
        private RateLimiterFactoryInterface $apiByUserLimiter
    ) {
    }

    public function isMainRequest(ControllerEvent $event): bool
    {
        return $event->getRequestType() === HttpKernelInterface::MAIN_REQUEST;
    }

    public function routeShouldBeLimited(Request $request): bool
    {
        $routesToLimit = [
            'api_auth_registration',
            'api_auth_me'
        ];
        $route = $request->attributes->get('_route');
        return $route && in_array($route, $routesToLimit, true);
    }

    public function limitShouldBeApplied(ControllerEvent $event): bool
    {
        return $this->isMainRequest($event) && $this->routeShouldBeLimited($event->getRequest());
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$this->limitShouldBeApplied($event)) {
            return;
        }

        $request = $event->getRequest();
        $user = $this->security->getUser();

        $factory = $this->registerByIpLimiter;
        $key = $request->getClientIp();
        if ($user) {
            $factory = $this->apiByUserLimiter;
            $key = $user->getUserIdentifier();
        }

        $limiter = $factory->create($key);
        if (!$limiter->consume()->isAccepted()) {
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
