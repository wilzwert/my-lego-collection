<?php

namespace App\Tests\Shared\Domain\Exception;

use App\Shared\Domain\Exception\ErrorCode;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */
class ErrorCodeTest extends TestCase
{
    #[Test]
    public function doSomething(): void
    {
        $this->expectNotToPerformAssertions();
        var_dump(ErrorCode::UNKNOWN_ERROR->name);
    }
}
