<?php

namespace App\Tests\User\Infrastructure\EventSubscriber;

/**
 * @author Wilhelm Zwertvaegher
 */

use App\User\Infrastructure\EventSubscriber\UserRateLimitSubscriber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimit;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserRateLimitSubscriberTest extends TestCase
{

    #[Test]
    public function isMainRequest_shouldReturnFalse_whenRequestIsNotMainRequest(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent(
            $kernel,
            fn () => null,
            new Request(),
            HttpKernelInterface::SUB_REQUEST
        );
        $security = $this->createMock(Security::class);
        $factory = $this->createMock(RateLimiterFactoryInterface::class);
        $factory->expects($this->never())->method('create');

        $subscriber = new UserRateLimitSubscriber($security, $factory, $factory, $factory);
        $this->assertFalse($subscriber->isMainRequest($event));
    }

    #[Test]
    public function isMainRequest_shouldReturnTrue_whenRequestIsMainRequest(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent(
            $kernel,
            fn () => null,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST
        );
        $security = $this->createMock(Security::class);
        $factory = $this->createMock(RateLimiterFactoryInterface::class);
        $factory->expects($this->never())->method('create');

        $subscriber = new UserRateLimitSubscriber($security, $factory, $factory, $factory);
        $this->assertTrue($subscriber->isMainRequest($event));
    }

    public static function routesProvider(): array
    {
        return [
            ['api_user_register', true],
            ['api_sets_search', true],
            ['public_route', false],
            [null, false],
        ];
    }

    #[Test]
    #[DataProvider('routesProvider')]
    public function testRouteShouldBeLimited(?string $route, bool $expected): void
    {
        $request = new Request([], [], ['_route' => $route], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $security = $this->createMock(Security::class);
        $factory = $this->createMock(RateLimiterFactoryInterface::class);
        $factory->expects($this->never())->method('create');

        $subscriber = new UserRateLimitSubscriber($security, $factory, $factory, $factory);
        $this->assertSame($expected, $subscriber->routeShouldBeLimited($request));
    }


    public static function eventsProvider(): array
    {
        return [
            [HttpKernelInterface::MAIN_REQUEST, 'api_user_register', true],
            [HttpKernelInterface::SUB_REQUEST, 'api_user_register', false],
            [HttpKernelInterface::MAIN_REQUEST, 'api_sets_search', true],
            [HttpKernelInterface::SUB_REQUEST, 'api_sets_search', false],
            [HttpKernelInterface::MAIN_REQUEST, 'public_route', false],
            [HttpKernelInterface::SUB_REQUEST, 'public_route', false],
            [HttpKernelInterface::MAIN_REQUEST, null, false],
            [HttpKernelInterface::SUB_REQUEST, null, false],
        ];
    }
    #[DataProvider('eventsProvider')]
    public function testLimitShouldBeApplied(int $requestMainOrNot, ?string $route, bool $expected): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent(
            $kernel,
            fn () => null,
            new Request([], [], ['_route' => $route], [], [], ['REMOTE_ADDR' => '127.0.0.1']),
            $requestMainOrNot
        );

        $security = $this->createMock(Security::class);
        $factory = $this->createMock(RateLimiterFactoryInterface::class);
        $factory->expects($this->never())->method('create');

        $subscriber = new UserRateLimitSubscriber($security, $factory, $factory, $factory);
        $this->assertSame($expected, $subscriber->limitShouldBeApplied($event));
    }

    #[Test]
    public function shouldSubscribeToControllerEvent(): void
    {
        $events = UserRateLimitSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::CONTROLLER, $events);
        $this->assertSame('onKernelController', $events[KernelEvents::CONTROLLER]);
    }

    /**
     *
     * Tests that when the request is not the main one, nothing happens, including no calls to limiter factories
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    #[Test]
    public function shouldSkipIfNotMainRequest(): void
    {
        $security = $this->createMock(Security::class);

        $registerByIpLimiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $registerByIpLimiterFactory->expects($this->never())->method('create');

        $publicApiByIpLimiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $publicApiByIpLimiterFactory->expects($this->never())->method('create');

        $apiByUserLimiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $apiByUserLimiterFactory->expects($this->never())->method('create');

        $subscriber = new UserRateLimitSubscriber(
            $security,
            $registerByIpLimiterFactory,
            $publicApiByIpLimiterFactory,
            $apiByUserLimiterFactory
        );

        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent(
            $kernel,
            fn () => null,
            new Request(),
            HttpKernelInterface::SUB_REQUEST
        );

        $subscriber->onKernelController($event);

        $this->assertTrue(true); // no exception = success
    }

    /**
     *
     * Tests that when a route is not limited, nothing happens, including no calls to limiter factories
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    #[Test]
    public function shouldSkipIfRouteNotLimited(): void
    {
        $security = $this->createMock(Security::class);

        $factory = $this->createMock(RateLimiterFactoryInterface::class);
        $factory->expects($this->never())->method('create');

        $subscriber = new UserRateLimitSubscriber(
            $security,
            $factory,
            $factory,
            $factory
        );

        $request = new Request();
        $request->attributes->set('_route', 'some_other_route');

        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->onKernelController($event);

        $this->assertTrue(true);
    }

    /**
     *
     * Tests that the specific factory is used on user registration route, and other factories are not used
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    #[Test]
    public function shouldUseRegisterByIpLimiter_forRegistrationRoute(): void
    {
        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->method('consume')->willReturn(new RateLimit(1, new \DateTimeImmutable(), true, 1));

        $registerByIpLimiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $registerByIpLimiterFactory->expects($this->once())->method('create')->with('127.0.0.1')->willReturn($limiter);

        $unusedFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $unusedFactory->expects($this->never())->method('create');

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $subscriber = new UserRateLimitSubscriber(
            $security,
            $registerByIpLimiterFactory,
            $unusedFactory,
            $unusedFactory
        );

        $request = new Request([], [], ['_route' => 'api_user_register'], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->onKernelController($event);
        $this->assertTrue(true);
    }

    #[Test]
    public function shouldThrow_whenRateLimitExceeded_inRegisterByIpLimiter(): void
    {
        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->method('consume')->willReturn(new RateLimit(0, new \DateTimeImmutable(), false, 1));

        $registerByIpLimiter = $this->createMock(RateLimiterFactoryInterface::class);
        $registerByIpLimiter->expects($this->once())->method('create')->with('127.0.0.1')->willReturn($limiter);

        $unusedFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $unusedFactory->expects($this->never())->method('create');

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $subscriber = new UserRateLimitSubscriber(
            $security,
            $registerByIpLimiter,
            $unusedFactory,
            $unusedFactory
        );

        $request = new Request([], [], ['_route' => 'api_user_register'], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->expectException(TooManyRequestsHttpException::class);

        $subscriber->onKernelController($event);
    }

    #[Test]
    public function shouldUseUserLimiter_whenUserIsAuthenticated(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user123');

        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->method('consume')->willReturn(new RateLimit(1, new \DateTimeImmutable(), true, 1));

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $factory = $this->createMock(RateLimiterFactoryInterface::class);
        $factory->expects($this->once())->method('create')->with('user123')->willReturn($limiter);
        $unusedFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $unusedFactory->expects($this->never())->method('create');

        $subscriber = new UserRateLimitSubscriber(
            $security,
            $unusedFactory,
            $unusedFactory,
            $factory
        );

        $request = new Request([], [], ['_route' => 'api_sets_search'], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->onKernelController($event);
        $this->assertTrue(true);
    }

    #[Test]
    public function shouldThrow_whenRateLimitExceeded_inApiByUserLimiter(): void
    {
        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->method('consume')->willReturn(new RateLimit(0, new \DateTimeImmutable(), false, 1));

        $userByIpLimiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $userByIpLimiterFactory->expects($this->once())->method('create')->with('user123')->willReturn($limiter);

        $unusedFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $unusedFactory->expects($this->never())->method('create');

        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('user123');

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $subscriber = new UserRateLimitSubscriber(
            $security,
            $unusedFactory,
            $unusedFactory,
            $userByIpLimiterFactory
        );

        $request = new Request([], [], ['_route' => 'api_sets_search'], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->expectException(TooManyRequestsHttpException::class);

        $subscriber->onKernelController($event);
    }

    #[Test]
    public function shouldUsePublicApiLimiter_whenUserIsNotAuthenticated(): void
    {
        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->method('consume')->willReturn(new RateLimit(1, new \DateTimeImmutable(), true, 1));

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $publicApiFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $publicApiFactory->expects($this->once())->method('create')->with('127.0.0.1')->willReturn($limiter);
        $unusedFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $unusedFactory->expects($this->never())->method('create');

        $subscriber = new UserRateLimitSubscriber(
            $security,
            $unusedFactory,
            $publicApiFactory,
            $unusedFactory
        );

        $request = new Request([], [], ['_route' => 'api_sets_search'], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $subscriber->onKernelController($event);
        $this->assertTrue(true);
    }

    #[Test]
    public function shouldThrow_whenRateLimitExceeded_inPublicApiLimiter(): void
    {
        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->method('consume')->willReturn(new RateLimit(0, new \DateTimeImmutable(), false, 1));

        $publicApiLimiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $publicApiLimiterFactory->expects($this->once())->method('create')->with('127.0.0.1')->willReturn($limiter);

        $unusedFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $unusedFactory->expects($this->never())->method('create');

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $subscriber = new UserRateLimitSubscriber(
            $security,
            $unusedFactory,
            $publicApiLimiterFactory,
            $unusedFactory
        );

        $request = new Request([], [], ['_route' => 'api_sets_search'], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->expectException(TooManyRequestsHttpException::class);

        $subscriber->onKernelController($event);
    }
}
