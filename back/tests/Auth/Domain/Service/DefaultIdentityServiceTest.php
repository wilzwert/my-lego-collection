<?php

namespace App\Tests\Auth\Domain\Service;

use App\Auth\Application\Command\ChangeEmailCommand;
use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Repository\IdentityRepository;
use App\Auth\Domain\Service\DefaultIdentityService;
use App\Auth\Domain\Service\PasswordHasher;
use App\Shared\Domain\Exception\EntityAlreadyExistsException;
use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Service\TransactionProvider;
use App\Auth\Application\Command\RegistrationCommand;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use App\Tests\Utilities\TestData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
final class DefaultIdentityServiceTest extends TestCase
{
    private PasswordHasher&MockObject $passwordHasher;

    private DefaultIdentityService $underTest;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(PasswordHasher::class);

        $this->underTest = new DefaultIdentityService(
            $this->passwordHasher
        );
    }

    public function testCreateIdentity(): void
    {
        $this->passwordHasher->expects(self::once())->method('hash')->willReturn('hashed_password');
        $identity = $this->underTest->createIdentity('test@email.com', 'username', 'password');

        self::assertInstanceOf(Identity::class, $identity);
        self::assertEquals('test@email.com', $identity->getEmail());
        self::assertEquals('username', $identity->getUsername());
        self::assertEquals('hashed_password', $identity->getPasswordHash());
        self::assertCount(1, $identity->getRoles());
        self::assertEquals('ROLE_USER', $identity->getRoles()[0]);
        $events = $identity->pullEvents();
        self::assertCount(1, $events);
        self::assertInstanceOf(IdentityCreatedEvent::class, $events[0]);

    }


}
