<?php

namespace App\Tests\CollectionManagement\Integration\Infrastructure\EventHandler;

use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\CollectionManagement\Domain\Port\Driven\ElementRepository;
use App\CollectionManagement\Domain\Port\Driven\SetElementRepository;
use App\CollectionManagement\Domain\Port\Driven\SetRepository;
use App\CollectionManagement\Infrastructure\EventHandler\CompleteSetCommandHandler;
use App\DataFixtures\TestData;
use App\Shared\Domain\Exception\InvalidEntityIdException;
use App\Shared\Domain\Model\EntityId;
use App\Tests\Traits\MessengerTestingTrait;
use App\Tests\Traits\TestResourcesTrait;
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
class CompleteSetCommandHandlerIT extends KernelTestCase
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
    public function whenSetComplete_thenCompleteSetCommand_shouldDoNothing(): void
    {
        $container = self::getContainer();
        /** @var TransportInterface $asyncTransport */
        $asyncTransport = $container->get('messenger.transport.async');

        $handler = $container->get(CompleteSetCommandHandler::class);

        $handler(new CompleteSetCommand(TestData::COMPLETE_SET_ID));

        $asyncEvent = $this->getTransportMatchingMessage(
            $asyncTransport,
            CompleteUserSetCommand::class
        );
        self::assertNull($asyncEvent);
    }


    /**
     * Tests that the CompleteSetCommand on an incomplete set is handled :
     * - elements creation,
     * - set elements creation
     * - CompleteUserSetCommand dispatched on related incomplete UserSet
     * @return void
     * @throws InvalidEntityIdException
     * @throws Exception
     */
    #[Test]
    public function shouldCompleteSet(): void
    {
        $container = self::getContainer();

        // mock the rebrickable API return for the set to complete
        $json = file_get_contents($this->getTestResourcePath('json/set_parts_test-incomplete.json'));
        $array = json_decode($json, true);
        $httpClient =  $this->createMock(HttpClientInterface::class);
        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('toArray')->willReturn($array);
        $httpClient->expects($this->once())->method('request')->willReturn($response);
        $container->set(HttpClientInterface::class, $httpClient);

        $handler = $container->get(CompleteSetCommandHandler::class);
        /** @var SetRepository $setRepository */
        $setRepository = $container->get(SetRepository::class);
        /** @var SetElementRepository $setElementRepository */
        $setElementRepository = $container->get(SetElementRepository::class);
        /** @var ElementRepository $elementRepository */
        $elementRepository = $container->get(ElementRepository::class);
        /** @var EntityManagerInterface $entityManager needed to force Set reload and check its actual state */
        $entityManager = $container->get(EntityManagerInterface::class);

        /** @var TransportInterface $asyncTransport */
        $asyncTransport = $container->get('messenger.transport.async');

        $handler(new CompleteSetCommand(TestData::INCOMPLETE_SET_ID));

        // test side effects
        // set should now be COMPLETED
        // force entity manager to clear its graph to force reloading
        $entityManager->clear();
        $set = $setRepository->findById(EntityId::fromString(TestData::INCOMPLETE_SET_ID));
        self::assertEquals(SetCreationStatus::COMPLETED, $set->getCreationStatus());

        // 3 elements should have been created for the set, including 1 with spare count > 0
        $setElements = $setElementRepository->findBySetId(EntityId::fromString(TestData::INCOMPLETE_SET_ID));
        self::assertCount(3, $setElements);

        // we should be able to load the related Sets and check their contents
        $elements = $elementRepository->findByIds(array_map(fn (SetElement $setElement) => $setElement->getElementId(), $setElements));
        $elementsByExternalId = $elementsById = [];
        // we will need to be able to access elements by their id and externalId
        foreach ($elements as $element) {
            $elementsByExternalId[$element->getExternalId()] = $element;
            $elementsById[$element->getId()->value()] = $element;
        }

        // names of the elements should be a concatenation of Part name with color name
        $elementsExpectations = [
            '306226' => ['name' => 'Brick Round 1 x 1 Open Stud - Black'],
            '6275806' => ['name' => 'Brick Special 1 x 2 x 1 2/3 with 4 Studs on 1 Side - Black'],
            '23110059' => ['name' => 'Bracket 1 x 2 - 1 x 4 [Rounded Corners at Bottom, Square Corners at Top] - White']
        ];

        foreach ($elementsByExternalId as $externalId => $element) {
            $expectation = $elementsExpectations[$externalId];
            self::assertEquals($expectation['name'], $element->getName());
        }

        // we will need to be able to access SetElements by their related element's externalId for checks
        $setElementsByExternalElementId = [];
        foreach ($setElements as $setElement) {
            $setElementsByExternalElementId[$elementsById[$setElement->getElementId()->value()]->getExternalId()] = $setElement;
        }

        $setElementsExpectations = [
            '306226' => ['count' => 5, 'spareCount' => 1],
            '6275806' => ['count' => 12, 'spareCount' => 0],
            '23110059' => ['count' => 2, 'spareCount' => 0]
        ];
        foreach ($setElementsByExternalElementId as $externalId => $setElement) {
            $expectation = $setElementsExpectations[$externalId];
            self::assertEquals($expectation['count'], $setElement->getCount());
            self::assertEquals($expectation['spareCount'], $setElement->getSpareCount());
        }


        // CompleteUserSetCommand should have been dispatched on async transport for INCOMPLETE_USER1_SET_ID
        $asyncEvent = $this->getTransportMatchingMessage(
            $asyncTransport,
            CompleteUserSetCommand::class,
            null,
            fn (CompleteUserSetCommand $command) => TestData::INCOMPLETE_USER1_SET_ID === $command->getUserSetId()
        );
        self::assertNotNull($asyncEvent);

    }

}
