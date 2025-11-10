<?php

namespace App\Tests\Auth\Application\Command;

use App\Auth\Application\Command\GetIdentityQuery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

final class GetIdentityQueryTest extends TestCase
{
    #[Test]
    public function shouldExposeIdentifier(): void
    {
        $query = new GetIdentityQuery('user-123');

        self::assertSame('user-123', $query->id);
    }
}
