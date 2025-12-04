<?php

namespace App\Tests\Auth\Unit\Domain\Service;

use App\Auth\Domain\Event\EmailChangedEvent;
use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Port\Driven\EmailAvailabilityChecker;
use App\Auth\Domain\Port\Driven\IdentityAvailabilityChecker;
use App\Auth\Domain\Port\Driven\PasswordHasher;
use App\Auth\Domain\Service\DefaultIdentityService;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class DefaultIdentityServiceTest extends TestCase
{
    private PasswordHasher&MockObject $passwordHasher;

    private IdentityAvailabilityChecker&MockObject $identityAvailabilityChecker;

    private EmailAvailabilityChecker&MockObject $emailAvailabilityChecker;

    private DefaultIdentityService $underTest;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(PasswordHasher::class);
        $this->identityAvailabilityChecker = $this->createMock(IdentityAvailabilityChecker::class);
        $this->emailAvailabilityChecker = $this->createMock(EmailAvailabilityChecker::class);

        $this->underTest = new DefaultIdentityService(
            $this->passwordHasher,
            $this->identityAvailabilityChecker,
            $this->emailAvailabilityChecker
        );
    }

    public function shouldCreateIdentity(): void
    {
        $this->passwordHasher->expects(self::once())->method('hash')->willReturn('hashed_password');
        $this->identityAvailabilityChecker->expects(self::once())->method('isIdentityAvailable')->willReturn(true);
        $identity = $this->underTest->createIdentity('test@example.com', 'username', 'password');

        self::assertInstanceOf(Identity::class, $identity);
        self::assertEquals('test@example.com', $identity->getEmail());
        self::assertEquals('username', $identity->getUsername());
        self::assertEquals('hashed_password', $identity->getPasswordHash());
        self::assertCount(1, $identity->getRoles());
        self::assertEquals('ROLE_USER', $identity->getRoles()[0]);
        $events = $identity->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(IdentityCreatedEvent::class, $events[0]);
    }

    #[Test]
    public function whenIdentityExists_thenShouldThrowEntityExistsException(): void
    {
        $this->passwordHasher->expects(self::never())->method('hash');
        $this->identityAvailabilityChecker->expects(self::once())->method('isIdentityAvailable')->willReturn(false);

        self::expectException(EntityAlreadyExistsException::class);

        $identity = $this->underTest->createIdentity('test@email.com', 'username', 'password');
    }

    public function shouldChangeEmail(): void
    {
        $identity = AuthTestsUtility::generateIdentity(email: 'old@example.com');
        $this->emailAvailabilityChecker->expects(self::once())->method('isEmailAvailable')->willReturn(true);
        $updatedIdentity = $this->underTest->changeEmail($identity, 'test@email.com');
        self::assertNotSame($identity, $updatedIdentity);
        self::assertEquals('test@email.com', $updatedIdentity->getEmail());
        self::assertEquals($identity->getUsername(), $updatedIdentity->getUsername());
        self::assertEquals($identity->getPasswordHash(), $updatedIdentity->getPasswordHash());
        self::assertEquals($identity->getRoles(), $updatedIdentity->getRoles());
        $events = $updatedIdentity->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(EmailChangedEvent::class, $events[0]);
    }

    #[Test]
    public function whenSameEmail_thenChangeEmail_shouldDoNothing(): void
    {
        $identity = AuthTestsUtility::generateIdentity(email: 'test@example.com');
        $this->emailAvailabilityChecker->expects(self::never())->method('isEmailAvailable');
        $updatedIdentity = $this->underTest->changeEmail($identity, 'test@example.com');
        self::assertSame($identity, $updatedIdentity);
        $events = $updatedIdentity->pullEvents();
        self::assertCount(0, $events);
    }


}
