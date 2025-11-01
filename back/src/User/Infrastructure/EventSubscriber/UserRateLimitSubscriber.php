<?php

namespace App\User\Infrastructure\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This rate limit applies to the User 'slice' only
 * Other slices may have their own subscribers (or not)
 * TODO : maybe we should consider the rate limit a global feature instead or relying on each slice to implement it
 * Although it may seem 'cleaner' to have each slice implement it, it may result in code duplication
 * Moreover, should slices become isolated micro services, the rate limiting would probably be handled differently
 * We should make a decision, but it's not that important at the moment
 *
 * @author Wilhelm Zwertvaegher
 *
 */
readonly class UserRateLimitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security                    $security,
        private RateLimiterFactoryInterface $registerByIpLimiter,
        private RateLimiterFactoryInterface $publicApiByIpLimiter,
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
            'api_user_register',
            'api_sets_search',
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
        $route = $event->getRequest()->attributes->get('_route');
        $user = $this->security->getUser();

        $factory = $this->publicApiByIpLimiter;
        $key = $request->getClientIp();
        if ($route === 'api_user_register') {
            $factory = $this->registerByIpLimiter;
        } elseif ($user) {
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
