<?php

namespace App\Tests\User\Unit\Domain\Service;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Port\Driven\TransactionProvider;
use App\User\Domain\Model\User;
use App\User\Domain\Port\Driven\RetrieveUserForIdentity;
use App\User\Domain\Port\Driven\UserRepository;
use App\User\Domain\Service\DefaultUserService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class DefaultUserServiceTest extends TestCase
{
    private EntityId $identityId;

    private RetrieveUserForIdentity&MockObject $retrieveUserForIdentity;

    private UserRepository $userRepository;
    private TransactionProvider $transactionProvider;
    private DefaultUserService $underTest;

    protected function setUp(): void
    {
        $this->identityId = EntityId::fromString('87654321-e89b-42d3-a456-426614174000');

        $this->retrieveUserForIdentity = $this->createMock(RetrieveUserForIdentity::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->transactionProvider = $this->createMock(\App\Shared\Domain\Port\Driven\TransactionProvider::class);

        $this->underTest = new DefaultUserService(
            $this->retrieveUserForIdentity,
            $this->userRepository,
            $this->transactionProvider
        );
    }

    #[Test]
    public function shouldCreateUser(): void
    {
        $this->retrieveUserForIdentity
            ->expects(self::once())
            ->method('retrieveUser')
            ->with($this->identityId)
            ->willReturn(null);

        $result = $this->underTest->createUser($this->identityId);

        self::assertInstanceOf(User::class, $result);
        self::assertEquals($this->identityId, $result->getIdentityId());
        self::assertNull($result->getAvatar());
    }

    #[Test]
    public function whenUserExists_thenShouldNotCreateNew(): void
    {
        $expectedUser = $this->createMock(User::class);
        $this->retrieveUserForIdentity
            ->expects(self::once())
            ->method('retrieveUser')
            ->with($this->identityId)
            ->willReturn($expectedUser);


        $result = $this->underTest->createUser($this->identityId);

        self::assertSame($expectedUser, $result);
    }
}
