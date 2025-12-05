<?php

namespace App\Tests\Auth\Unit\Application\Orchestrator;

use App\Auth\Application\Orchestrator\IdentityCreatedOrchestrator;
use App\Auth\Domain\Event\IdentityCreatedEvent;
use App\Shared\Infrastructure\Messenger\CommandBus;
use App\Shared\Infrastructure\Messenger\IntegrationEventBus;
use App\Tests\Auth\Utilities\AuthTestsUtility;
use MyLegoCollection\SharedContracts\Command\CreateUserCommand;
use MyLegoCollection\SharedContracts\Event\IdentityCreatedIntegrationEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedOrchestratorTest extends TestCase
{
    private CommandBus&MockObject $commandBus;
    private IntegrationEventBus&MockObject $integrationBus;

    private IdentityCreatedOrchestrator $underTest;

    public function setUp(): void
    {
        parent::setUp();
        $this->integrationBus = $this->createMock(IntegrationEventBus::class);
        $this->commandBus = $this->createMock(CommandBus::class);
        $this->underTest = new IdentityCreatedOrchestrator($this->commandBus, $this->integrationBus);
    }

    #[Test]
    public function shouldDispatchCommandAndEvent(): void
    {
        $identity = AuthTestsUtility::generateKnownIdentity();
        $identityCreatedEvent = new IdentityCreatedEvent($identity);

        $this->commandBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (CreateUserCommand $command) use (&$dispatchedCommand) {
                $dispatchedCommand = $command;
                return true;
            }));

        $this->integrationBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (IdentityCreatedIntegrationEvent $event) use (&$dispatchedEvent) {
                $dispatchedEvent = $event;
                return true;
            }));

        ($this->underTest)($identityCreatedEvent);

        self::assertEquals($identity->getId(), $dispatchedCommand->getIdentityId());
        self::assertEquals($identity->getId(), $dispatchedEvent->getIdentityId());
    }

}
