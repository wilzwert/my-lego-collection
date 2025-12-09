<?php

namespace App\Tests\CollectionManagement\Utilities;

use App\CollectionManagement\Domain\Model\External\ExternalColor;
use App\CollectionManagement\Domain\Model\External\ExternalElement;
use App\CollectionManagement\Domain\Model\External\ExternalPart;
use App\CollectionManagement\Domain\Model\External\ExternalSetElement;
use App\CollectionManagement\Domain\Model\Local\Set;
use App\CollectionManagement\Domain\Model\Local\SetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\CollectionManagement\Domain\Model\Local\UserSetCreationStatus;
use App\CollectionManagement\Domain\Model\Local\UserSetStatus;
use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
class CollectionManagementTestsUtility
{

    public const string DEFAULT_EXISTING_SET_ID = '71486dd0-c4bb-43aa-849a-d6c04a6686cc';
    public const string DEFAULT_EXISTING_SET_EXTERNAL_ID = '71486dd0-c4bb-43aa-849a-d6c04a6686cc';
    public const string DEFAULT_SET_NAME = 'Star Wars Superstar Destroyer';
    public const string DEFAULT_SET_IMAGE_PATH = '/images/destroyer.png';

    public const string DEFAULT_SET_CREATED_AT = '2025-11-15T12:00:00';
    public const string DEFAULT_SET_UPDATED_AT = '2025-11-15T13:00:00';

    public static function generateKnownSet(): Set
    {
        return new Set(
            EntityId::fromString(self::DEFAULT_EXISTING_SET_ID),
            '30056-1',
            '30056',
            'Star Destroyer',
            38,
            'https://cdn.rebrickable.com/media/sets/30056-1/1837.jpg',
            2012,
            SetCreationStatus::COMPLETED,
            new \DateTimeImmutable(),
            new \DateTimeImmutable(),
        );
    }

    public static function generateSet(?string $id = null, ?string $externalId = null): Set
    {
        return new Set(
            $id ? EntityId::fromString($id) : EntityId::generate(),
            $externalId ?? uniqid('ext-', true),
            uniqid('lego-', true),
            uniqid('Set ', true),
            100,
            'image/set.png',
            floor(mt_rand(2010, 2015)),
            SetCreationStatus::COMPLETED,
            new \DateTimeImmutable(self::DEFAULT_SET_CREATED_AT),
            new \DateTimeImmutable(self::DEFAULT_SET_UPDATED_AT)
        );

    }

    public static function generateUserSet(Set $set, ?string $userSetId = null, ?string $userId = null): UserSet
    {
        return new UserSet(
            $userSetId ? EntityId::fromString($userSetId) : EntityId::generate(),
            $userId ? EntityId::fromString($userId) : EntityId::generate(),
            $set->getId(),
            new \DateTimeImmutable(),
            UserSetCreationStatus::COMPLETED,
            UserSetStatus::WANTED,
            new \DateTimeImmutable()
        );
    }
}
