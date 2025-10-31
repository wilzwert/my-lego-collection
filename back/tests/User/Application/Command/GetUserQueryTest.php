<?php

namespace App\Tests\User\Application\Command;

use App\User\Application\Command\GetUserQuery;
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
        $query = new GetUserQuery('user-123');

        $this->assertSame('user-123', $query->getIdentifier());
    }
}
