<?php

namespace App\Tests\User\Application\Command;

use App\User\Application\Command\GetUserByIdentityQuery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class GetUserQueryTest extends TestCase
{
    #[Test]
    public function shouldExposeIdentifier(): void
    {
        $query = new GetUserByIdentityQuery('user-123');

        $this->assertSame('user-123', $query->identityId);
    }
}
