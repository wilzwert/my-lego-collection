<?php

namespace App\DataFixtures;

use App\CollectionManagement\Domain\Model\External\ExternalColor;
use App\CollectionManagement\Domain\Model\External\ExternalElement;
use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSetElement;
use App\CollectionManagement\Domain\Model\Local\Color;
use App\CollectionManagement\Domain\Model\Local\Element;
use App\CollectionManagement\Domain\Model\Local\Part;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\SetElement;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetStatus;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineColor;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineElement;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrinePart;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineSet;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineSetElement;
use App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity\DoctrineUserSet;
use App\Shared\Domain\Model\EntityId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\TextUI\TestDirectoryNotFoundException;

/**
 * @author Wilhelm Zwertvaegher
 */
class CollectionManagementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // to test the CollectionManagement module, we need :
        // - some Parts, Colors and Elements
        $colors = [
            '10' => new ExternalColor('10', '37', 'Bright Green', '4B9F4A'),
            '191' => new ExternalColor('191', '191', 'Bright Light Orange', 'F8BB3D'),
            '226' => new ExternalColor('226', '226', 'Bright Light Yellow', 'FFF03A'),
            '27' => new ExternalColor('27', '119', 'Lime', 'BBE90B'),
            '322' => new ExternalColor('322', '322', 'Medium Azure', '36AEBF'),
            '41' => new ExternalColor('41', '42', 'Trans-Light Blue', 'AEEFEC'),
            '15' => new ExternalColor('15', '1', 'White', 'FFFFFF')
        ];

        $parts = [
            '3437' => new ExternalPart('3437', '17556', 'Duplo Brick 2 x 2', 'https://cdn.rebrickable.com/media/parts/elements/4168579.jpg'),
            '53920pr0003' => new ExternalPart('53920pr0003', '110434', 'Duplo Animal Lion Adult Female with Head turned Left with Black Eyes print', 'https://cdn.rebrickable.com/media/parts/elements/6520565.jpg'),
            '109575pr0002' => new ExternalPart('109575pr0002', '110435', 'Duplo Animal, Lion Cup with Black Eyes print', 'https://cdn.rebrickable.com/media/parts/elements/6520567.jpg'),
            '98233' => new ExternalPart('98233', '98233', 'Duplo Plate 2 x 6', 'https://cdn.rebrickable.com/media/parts/elements/4651836.jpg'),
            '3118' => new ExternalPart('3118', '3118', 'Duplo Plant / Leaf', 'https://cdn.rebrickable.com/media/parts/elements/6421351.jpg'),
            '40666' => new ExternalPart('40666', '40666', 'Duplo Plate 2 x 4', 'https://cdn.rebrickable.com/media/parts/elements/6211342.jpg'),
            '84210pr0002' => new ExternalPart('84210pr0002', '110433', 'Duplo Animal Penguin, Baby with Light Bluish Grey Back, Black Beak Print', 'https://cdn.rebrickable.com/media/parts/elements/6520533.jpg'),
            '110432pr0001' => new ExternalPart('110432pr0001', '110432', 'Duplo Animal Penguin Large with White/Yellow Chest Print', 'https://cdn.rebrickable.com/media/parts/elements/6520531.jpg'),
            '35114' => new ExternalPart('35114', '35114', 'Duplo Brick 3 x 2 Slope 33°', 'https://cdn.rebrickable.com/media/parts/elements/6294369.jpg'),
        ];

        $elements = [
            '4583789' => new ExternalElement('4583789', '4583789', '3437', 'https://cdn.rebrickable.com/media/parts/elements/4583789.jpg', '10'),
            '6520565' => new ExternalElement('6520565', '6520565', '53920pr0003', 'https://cdn.rebrickable.com/media/parts/elements/6520565.jpg', '191'),
            '6520567' => new ExternalElement('6520567', '6520567', '109575pr0002', 'https://cdn.rebrickable.com/media/parts/elements/6520567.jpg', '191'),
            '6486219' => new ExternalElement('6486219', '6486219', '98233', 'https://cdn.rebrickable.com/media/parts/elements/6486219.jpg', '226'),
            '6421351' => new ExternalElement('6421351', '6421351', '3118', 'https://cdn.rebrickable.com/media/parts/elements/6421351.jpg', '27'),
            '6211342' => new ExternalElement('6211342', '6211342', '40666', 'https://cdn.rebrickable.com/media/parts/elements/6211342.jpg', '322'),
            '6507900' => new ExternalElement('6507900', '6507900', '3437', 'https://cdn.rebrickable.com/media/parts/elements/6507900.jpg', '41'),
            '6520533' => new ExternalElement('6520533', '6520533', '84210pr0002', 'https://cdn.rebrickable.com/media/parts/elements/6520533.jpg', '15'),
            '6520531' => new ExternalElement('6520531', '6520531', '110432pr0001', 'https://cdn.rebrickable.com/media/parts/elements/6520531.jpg', '15'),
            '6526715' => new ExternalElement('6526715', '6526715', '35114', 'https://cdn.rebrickable.com/media/parts/elements/6294369.jpg', '15'),
        ];

        $externalSetId = '10442-1';
        $setElements = [
            new ExternalSetElement($externalSetId, $elements['4583789'], $parts['3437'], $colors['10'], 1, 0),
            new ExternalSetElement($externalSetId, $elements['6520565'], $parts['53920pr0003'], $colors['191'], 1, 0),
            new ExternalSetElement($externalSetId, $elements['6520567'], $parts['109575pr0002'], $colors['191'], 1, 0),
            new ExternalSetElement($externalSetId, $elements['6486219'], $parts['98233'], $colors['226'], 1, 0),
            new ExternalSetElement($externalSetId, $elements['6421351'], $parts['3118'], $colors['27'], 1, 0),
            new ExternalSetElement($externalSetId, $elements['6211342'], $parts['40666'], $colors['322'], 1, 0),
            new ExternalSetElement($externalSetId, $elements['6507900'], $parts['3437'], $colors['41'], 1, 0),
            new ExternalSetElement($externalSetId, $elements['6520533'], $parts['84210pr0002'], $colors['15'], 1, 0),
            new ExternalSetElement($externalSetId, $elements['6520531'], $parts['110432pr0001'], $colors['15'], 1, 0),
            new ExternalSetElement($externalSetId, $elements['6526715'], $parts['35114'], $colors['15'], 1, 0),
        ];

        $doctrineColors = $doctrineParts  = $doctrineElements = [];

        foreach ($colors as $externalColorId => $externalColor) {
            $doctrineColor = new DoctrineColor()->fromDomain(new Color(
                EntityId::generate(),
                $externalColorId,
                $externalColor->getLegoId(),
                $externalColor->getName(),
                $externalColor->getRgbCode()
            ));
            $manager->persist($doctrineColor);
            $doctrineColors[$externalColorId] = $doctrineColor;
        }

        foreach ($parts as $externalPartId => $externalPart) {
            $doctrinePart = new DoctrinePart()->fromDomain(new Part(
                EntityId::generate(),
                $externalPartId,
                $externalPart->getLegoId(),
                $externalPart->getName(),
                $externalPart->getImagePath()
            ));
            $manager->persist($doctrinePart);
            $doctrineParts[$externalPartId] = $doctrinePart;
        }

        foreach ($elements as $externalElementId => $externalElement) {
            $doctrinePart = $doctrineParts[$externalElement->getExternalPartId()];
            $doctrineColor = $doctrineColors[$externalElement->getExternalColorId()];
            $doctrineElement = new DoctrineElement()->fromDomain(new Element(
                EntityId::generate(),
                EntityId::fromString($doctrinePart->getId()),
                EntityId::fromString($doctrineColor->getId()),
                $externalElementId,
                $doctrinePart->getName().' - '.$doctrineColor->getName(),
                $externalElement->getImagePath()
            ));
            $manager->persist($doctrinePart);
            $doctrineElements[$externalElementId] = $doctrineElement;
        }

        // - a complete Set with some SetElements, to check that :
        //      - a CompleteSetCommand on the Set does nothing,
        //      - UserSet creation for this Set for User2 dispatches the CompleteUserSetCommand
        $doctrineCompleteSet = new DoctrineSet()->fromDomain(new Set(
            EntityId::fromString(TestData::COMPLETE_SET_ID),
            $externalSetId,
            '10442',
            'Wild Animal Families: Penguins & Lions',
            10,
            'https://cdn.rebrickable.com/media/sets/10442-1/149586.jpg',
            2025,
            SetCreationStatus::COMPLETED,
            new \DateTimeImmutable('2025-11-10T12:00:00'),
            new \DateTimeImmutable('2025-11-10T13:00:00')
        ));
        $manager->persist($doctrineCompleteSet);

        foreach ($setElements as $externalSetElement) {
            $doctrineSetElement = new DoctrineSetElement()->fromDomain(new SetElement(
                EntityId::generate(),
                EntityId::fromString($doctrineCompleteSet->getId()),
                EntityId::fromString($doctrineElements[$externalSetElement->getExternalElement()->getExternalId()]->getId()),
                $externalSetElement->getQuantity(),
                $externalSetElement->getSpareQuantity()
            ));
            $manager->persist($doctrineSetElement);
        }

        // - a complete UserSet for User1 linked to the complete Set to check that
        //      - a CompleteUserSetCommand on the UserSet does nothing
        //      - UserSet creation for the complete Set for User1 does nothing (or throws ?), as it already exists
        $doctrineCompleteUserSet = new DoctrineUserSet()->fromDomain(
            new UserSet(
                EntityId::fromString(TestData::COMPLETE_USER1_SET_ID),
                EntityId::fromString(TestData::USER1_ID),
                $doctrineCompleteSet->toDomain()->getId(),
                new \DateTimeImmutable('2025-11-12T12:00:00'),
                UserSetCreationStatus::COMPLETED,
                UserSetStatus::BUILT,
                new \DateTimeImmutable('2025-11-12T12:02:00')
            ),
            $doctrineCompleteSet
        );
        $manager->persist($doctrineCompleteUserSet);


        // - an incomplete Set to check that
        //      - a CompleteSetCommand triggers external data fetch and local data creation
        //      - the Set completion dispatches the CompleteUserSetCommand
        $doctrineIncompleteSet = new DoctrineSet()->fromDomain(new Set(
            EntityId::fromString(TestData::INCOMPLETE_SET_ID),
            'test-incomplete',
            '11111',
            'My custom test set',
            6,
            'https://cdn.rebrickable.com/media/sets/40675-1/139285.jpg',
            2022,
            SetCreationStatus::CREATED,
            new \DateTimeImmutable('2025-11-13T12:00:00'),
            new \DateTimeImmutable('2025-11-13T12:00:00')
        ));
        $manager->persist($doctrineIncompleteSet);


        // - a created UserSet linked to the incomplete Set to check that
        //      - a CompleteUserSetCommand does nothing when the linked Set is not complete
        $doctrineCreatedUserSet = new DoctrineUserSet()->fromDomain(
            new UserSet(
                EntityId::fromString(TestData::INCOMPLETE_USER1_SET_ID),
                EntityId::fromString(TestData::USER1_ID),
                $doctrineIncompleteSet->toDomain()->getId(),
                new \DateTimeImmutable('2025-11-13T12:00:00'),
                UserSetCreationStatus::CREATED,
                UserSetStatus::BUILT,
                new \DateTimeImmutable('2025-11-13T12:00:00'),
            ),
            $doctrineIncompleteSet
        );
        $manager->persist($doctrineCreatedUserSet);

        // - a created UserSet linked to a complete Set to check that
        //      - a CompleteUserSetCommand triggers data creation
        $doctrineCreatedUserSet = new DoctrineUserSet()->fromDomain(
                new UserSet(
                EntityId::fromString(TestData::INCOMPLETE_USER2_SET_ID),
                EntityId::fromString(TestData::USER2_ID),
                $doctrineCompleteSet->toDomain()->getId(),
                new \DateTimeImmutable('2025-11-14T12:00:00'),
                UserSetCreationStatus::CREATED,
                UserSetStatus::BUILT,
                new \DateTimeImmutable('2025-11-14T12:00:00')
            ),
            $doctrineCompleteSet
        );
        $manager->persist($doctrineCreatedUserSet);

        // Set creation and UserSet creation will also be tested, mainly to check that a CompleteUserSetCommand is not dispatched when creating a UserSet for an incomplete Set
        $manager->flush();
    }
}
