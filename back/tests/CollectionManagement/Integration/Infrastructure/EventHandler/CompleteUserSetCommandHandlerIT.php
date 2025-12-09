<?php

namespace App\Tests\CollectionManagement\Integration\Infrastructure\EventHandler;

use App\CollectionManagement\Domain\Event\UserSetCompletedEvent;
use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Port\Driven\ElementRepository;
use App\CollectionManagement\Domain\Port\Driven\SetElementRepository;
use App\CollectionManagement\Domain\Port\Driven\SetRepository;
use App\CollectionManagement\Domain\Port\Driven\UserElementRepository;
use App\CollectionManagement\Domain\Port\Driven\UserSetElementRepository;
use App\CollectionManagement\Domain\Port\Driven\UserSetRepository;
use App\CollectionManagement\Infrastructure\EventHandler\CompleteSetCommandHandler;
use App\CollectionManagement\Infrastructure\EventHandler\CompleteUserSetCommandHandler;
use App\DataFixtures\TestData;
use App\Shared\Domain\Exception\InvalidEntityIdException;
use App\Shared\Domain\Model\EntityId;
use App\Tests\Traits\MessengerTestingTrait;
use App\Tests\Traits\TestResourcesTrait;
use App\Tests\Utilities\DummySyncDomainEventHandler;
use App\Tests\Utilities\DummySyncIntegrationEventHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use MyLegoCollection\SharedContracts\Command\CompleteSetCommand;
use MyLegoCollection\SharedContracts\Command\CompleteUserSetCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @author Wilhelm Zwertvaegher
 */
class CompleteUserSetCommandHandlerIT extends KernelTestCase
{

    use MessengerTestingTrait;

    use TestResourcesTrait;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->resetMessenger();
        parent::setUp();
    }


    #[Test]
    public function whenUserSetComplete_thenCompleteUserSetCommand_shouldDoNothing(): void
    {
        $container = self::getContainer();

        $handler = $container->get(CompleteUserSetCommandHandler::class);


        self::expectNotToPerformAssertions();
        $handler(new CompleteUserSetCommand(TestData::COMPLETE_USER1_SET_ID));

        // TODO : how to check nothing has been emitted ?


    }


    /**
     * Tests that the CompleteUserSetCommand on an incomplete owned UserSet is handled :
     * - user_set_elements creation,
     * - user_elements creation/update
     * - UserSetCompletedEvent dispatched
     * @return void
     * @throws InvalidEntityIdException
     * @throws Exception
     */
    #[Test]
    public function shouldCompleteUserSet(): void
    {
        $container = self::getContainer();

        /** @var DummySyncDomainEventHandler $dummyHandler */
        $dummyHandler = $container->get(DummySyncDomainEventHandler::class);
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        /** @var CompleteUserSetCommandHandler $handler */
        $handler = $container->get(CompleteUserSetCommandHandler::class);
        /** @var UserSetRepository $userSetRepository */
        $userSetRepository = $container->get(UserSetRepository::class);
        /** @var SetElementRepository $setElementRepository */
        $setElementRepository = $container->get(SetElementRepository::class);
        /** @var UserSetElementRepository $userSetElementRepository */
        $userSetElementRepository = $container->get(UserSetElementRepository::class);
        /** @var UserElementRepository $userElementRepository */
        $userElementRepository = $container->get(UserElementRepository::class);


        // TODO : also check the UserElement when it is implemented

        $handler(new CompleteUserSetCommand(TestData::INCOMPLETE_USER2_SET_ID));

        // clear the entity manager forces entities reloading to ensure data has been appropriately saved
        $entityManager->clear();

        // test side effects
        // user set should now be COMPLETED
        $userSet = $userSetRepository->findById(EntityId::fromString(TestData::INCOMPLETE_USER2_SET_ID));
        self::assertEquals(UserSetCreationStatus::COMPLETED, $userSet->getCreationStatus());

        // 3 user_set_elements should have been created for the user_set, including 1 with spare count > 0
        $userSetElements = $userSetElementRepository->findByUserSetId($userSet->getId());
        self::assertCount(10, $userSetElements);
        $userSetElementsByElementId = [];
        foreach ($userSetElements as $userSetElement) {
            $userSetElementsByElementId[$userSetElement->getElementId()->value()] = $userSetElement;
        }
        // compare UserSetElements count and spareCount with related SetElements
        $setElements = $setElementRepository->findBySetId($userSet->getId());
        foreach ($setElements as $setElement) {
            self::assertEquals($setElement->getCount(), $userSetElementsByElementId[$setElement->getId()->value()]->getCount());
            self::assertEquals($setElement->getSpareCount(), $userSetElementsByElementId[$setElement->getId()->value()]->getSpareCount());
        }

        // check user_elements have been created/updated
        $userElements = $userElementRepository->findByUserId($userSet->getUserId());
        self::assertCount(10, $userElements);

        // check UserSetCompletedEvent has been dispatched
        self::assertTrue(
            $this->handlerHas(
                $dummyHandler,
                UserSetCompletedEvent::class,
                fn (UserSetCompletedEvent $e) => $e->getUserSet()->getId()->value() === $userSet->getId()->value()
            )
        );
    }

}
