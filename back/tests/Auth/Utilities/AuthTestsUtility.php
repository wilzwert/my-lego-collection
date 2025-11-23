<?php

namespace App\Tests\Auth\Utilities;

use App\Auth\Domain\Model\Identity;
use App\Shared\Domain\Model\EntityId;

/**
 * @author Wilhelm Zwertvaegher
 */
class AuthTestsUtility
{

    public const string DEFAULT_EMAIL = 'user@example.com';
    public const string DEFAULT_USERNAME = 'user';

    public const string DEFAULT_PASSWORD_HASH = 'hash';


    public static function generateIdentity(
        ?EntityId $entityId = null,
        ?string   $email = self::DEFAULT_EMAIL,
        ?string   $username = self::DEFAULT_USERNAME,
        ?string   $passwordHash = self::DEFAULT_PASSWORD_HASH,
        ?array    $roles = ['ROLE_USER']
    ): Identity
    {
        return new Identity($entityId ?? EntityId::generate(), $email, $username, $passwordHash, $roles);
    }

}
