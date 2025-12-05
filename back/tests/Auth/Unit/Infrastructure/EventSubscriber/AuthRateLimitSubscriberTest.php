<?php

namespace App\Tests\Auth\Unit\Infrastructure\EventSubscriber;

/**
 * @author Wilhelm Zwertvaegher
 */

use App\Auth\Infrastructure\EventSubscriber\AuthRateLimitSubscriber;
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

final class AuthRateLimitSubscriberTest extends TestCase
{

    private Security $security;

    private RateLimiterFactoryInterface $registerByIpLimiterFactory;

    private RateLimiterFactoryInterface $apiByUserLimiterFactory;

    /**
     * Under test
     * @var AuthRateLimitSubscriber
     */
    private AuthRateLimitSubscriber $subscriber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->security = $this->createStub(Security::class);
        $this->registerByIpLimiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $this->apiByUserLimiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $this->registerByIpLimiterFactory->expects($this->never())->method('create');
        $this->apiByUserLimiterFactory->expects($this->never())->method('create');
        $this->subscriber = new AuthRateLimitSubscriber($this->security, $this->registerByIpLimiterFactory, $this->apiByUserLimiterFactory);
    }

    private function setupSubscriber(?RateLimiterFactoryInterface $registerByIpLimiterFactory, ?RateLimiterFactoryInterface $apiByUserLimiterFactory, ?string $userIdentifier): void
    {
        if ($userIdentifier) {
            $user = $this->createMock(UserInterface::class);
            $user->method('getUserIdentifier')->willReturn($userIdentifier);
            $security = $this->createMock(Security::class);
            $security->method('getUser')->willReturn($user);
        } else {
            $security = $this->security;
        }

        $this->subscriber = new AuthRateLimitSubscriber(
            $security,
            $registerByIpLimiterFactory ?? $this->registerByIpLimiterFactory,
            $apiByUserLimiterFactory ?? $this->apiByUserLimiterFactory
        );
    }

    #[Test]
    public function isMainRequest_shouldReturnFalse_whenRequestIsNotMainRequest(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $event = new ControllerEvent(
            $kernel,
            fn () => null,
            new Request(),
            HttpKernelInterface::SUB_REQUEST
        );

        self::assertFalse($this->subscriber->isMainRequest($event));
    }

    #[Test]
    public function isMainRequest_shouldReturnTrue_whenRequestIsMainRequest(): void
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $event = new ControllerEvent(
            $kernel,
            fn () => null,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST
        );

        self::assertTrue($this->subscriber->isMainRequest($event));
    }

    public static function routesProvider(): array
    {
        return [
            ['api_auth_registration', true],
            ['api_auth_me', true],
            ['api_sets_search', false],
            ['public_route', false],
            [null, false],
        ];
    }

    #[Test]
    #[DataProvider('routesProvider')]
    public function testRouteShouldBeLimited(?string $route, bool $expected): void
    {
        $request = new Request([], [], ['_route' => $route], [], [], ['REMOTE_ADDR' => '127.0.0.1']);

        self::assertSame($expected, $this->subscriber->routeShouldBeLimited($request));
    }


    public static function eventsProvider(): array
    {
        return [
            [HttpKernelInterface::MAIN_REQUEST, 'api_auth_registration', true],
            [HttpKernelInterface::SUB_REQUEST, 'api_auth_registration', false],
            [HttpKernelInterface::MAIN_REQUEST, 'api_auth_me', true],
            [HttpKernelInterface::SUB_REQUEST, 'api_auth_me', false],
            [HttpKernelInterface::MAIN_REQUEST, 'api_sets_search', false],
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
        $kernel = $this->createStub(HttpKernelInterface::class);
        $event = new ControllerEvent(
            $kernel,
            fn () => null,
            new Request([], [], ['_route' => $route], [], [], ['REMOTE_ADDR' => '127.0.0.1']),
            $requestMainOrNot
        );

        self::assertSame($expected, $this->subscriber->limitShouldBeApplied($event));
    }

    #[Test]
    public function shouldSubscribeToControllerEvent(): void
    {
        $events = AuthRateLimitSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(KernelEvents::CONTROLLER, $events);
        self::assertSame('onKernelController', $events[KernelEvents::CONTROLLER]);
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
        $kernel = $this->createStub(HttpKernelInterface::class);
        $event = new ControllerEvent(
            $kernel,
            fn () => null,
            new Request(),
            HttpKernelInterface::SUB_REQUEST
        );

        $this->subscriber->onKernelController($event);

        self::assertTrue(true); // no exception = success
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
        $request = new Request();
        $request->attributes->set('_route', 'some_other_route');

        $kernel = $this->createStub(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->subscriber->onKernelController($event);

        self::assertTrue(true);
    }

    /**
     *
     * Tests that the factory is used on registration route
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    #[Test]
    public function shouldUseRegisterByIpLimiter_forRegistrationRoute(): void
    {
        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->expects($this->once())->method('consume')->willReturn(new RateLimit(1, new \DateTimeImmutable(), true, 1));

        $registerByIpLimiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $registerByIpLimiterFactory->expects($this->once())->method('create')->with('127.0.0.1')->willReturn($limiter);
        $this->setupSubscriber($registerByIpLimiterFactory, null, null);

        $request = new Request([], [], ['_route' => 'api_auth_registration'], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $kernel = $this->createStub(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->subscriber->onKernelController($event);
        self::assertTrue(true);
    }

    #[Test]
    public function shouldThrow_whenRateLimitExceeded_inRegistrationByIpLimiter(): void
    {
        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->expects($this->once())->method('consume')->willReturn(new RateLimit(0, new \DateTimeImmutable(), false, 1));

        $registerByIpLimiterFactory = $this->createMock(RateLimiterFactoryInterface::class);
        $registerByIpLimiterFactory->expects($this->once())->method('create')->with('127.0.0.1')->willReturn($limiter);
        $this->setupSubscriber($registerByIpLimiterFactory, null, null);

        $request = new Request([], [], ['_route' => 'api_auth_registration'], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $kernel = $this->createStub(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->expectException(TooManyRequestsHttpException::class);

        $this->subscriber->onKernelController($event);
    }

    /**
     *
     * Tests that the factory is used on registration route
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    #[Test]
    public function shouldUseApiByUserLimiter_forRegistrationRoute(): void
    {

        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->expects($this->once())->method('consume')->willReturn(new RateLimit(1, new \DateTimeImmutable(), true, 1));

        $apiByUserLimiter = $this->createMock(RateLimiterFactoryInterface::class);
        $apiByUserLimiter->expects($this->once())->method('create')->with('user123')->willReturn($limiter);
        $this->setupSubscriber(null, $apiByUserLimiter, 'user123');

        $request = new Request([], [], ['_route' => 'api_auth_me'], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $kernel = $this->createStub(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->subscriber->onKernelController($event);
        self::assertTrue(true);
    }

    #[Test]
    public function shouldThrow_whenRateLimitExceeded_inApiByUserLimiter(): void
    {
        $limiter = $this->createMock(LimiterInterface::class);
        $limiter->expects($this->once())->method('consume')->willReturn(new RateLimit(0, new \DateTimeImmutable(), false, 1));

        $apiByUserLimiter = $this->createMock(RateLimiterFactoryInterface::class);
        $apiByUserLimiter->expects($this->once())->method('create')->with('user123')->willReturn($limiter);
        $this->setupSubscriber(null, $apiByUserLimiter, 'user123');

        $request = new Request([], [], ['_route' => 'api_auth_me'], [], [], ['REMOTE_ADDR' => '127.0.0.1']);
        $kernel = $this->createStub(HttpKernelInterface::class);
        $event = new ControllerEvent($kernel, fn () => null, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->expectException(TooManyRequestsHttpException::class);

        $this->subscriber->onKernelController($event);
    }
}
