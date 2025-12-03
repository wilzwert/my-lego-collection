<?php

namespace App\Tests\User\Application\Handler;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Port\Driven\EventBus;
use App\Shared\Domain\Port\Driven\TransactionProvider;
use App\Tests\User\Utilities\UserTestsUtility;
use App\User\Application\Handler\CreateUserHandler;
use App\User\Domain\Model\User;
use App\User\Domain\Port\Driven\UserRepository;
use App\User\Domain\Service\UserService;
use MyLegoCollection\SharedEvent\Command\CreateUserCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class CreateUserHandlerTest extends TestCase
{
    #[Test]
    public function shouldInvokeUserServiceToCreateUser(): void
    {
        $identityId = EntityId::generate();
        $user = UserTestsUtility::generateUser(identityId: $identityId);
        $command = new CreateUserCommand($identityId->value());
        $userService = $this->createMock(UserService::class);
        $userRepository = $this->createMock(UserRepository::class);
        $transactionProvider = $this->createMock(TransactionProvider::class);
        $eventBus = $this->createMock(EventBus::class);

        $userService
            ->expects($this->once())
            ->method('createUser')
            ->with($identityId->__toString())
            ->willReturn($user);

        $userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (User $u) use (&$savedUser) {
                $savedUser = $u;
                return true;
            }));

        // simulate TransactionProvider behavior
        $transactionProvider
            ->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(
                // simulate transaction -> just execute callback
                fn (callable $callback) => $callback()
            );

        $eventBus
            ->expects($this->once())
            ->method('dispatchAll')
            ->with($this->callback(function (User $u) use (&$eventsUser) {
                $eventsUser = $u;
                return true;
            }));

        $handler = new CreateUserHandler($userService, $userRepository, $transactionProvider, $eventBus);
        $handler($command);

        self::assertSame($user, $savedUser);
        self::assertSame($user, $eventsUser);
    }
}
