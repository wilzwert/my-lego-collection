<?php

namespace App\Tests\Shared\Unit\Domain\Validation;

use App\Shared\Domain\Exception\ErrorCode;
use App\Shared\Domain\Validation\ValidationError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

class ValidationErrorTest extends TestCase
{
    #[Test]
    public function shouldGenerateToString(): void
    {
        $details = ['detailLabel' => 'detailDescription'];
        $error = new ValidationError("fieldName", ErrorCode::FIELD_CANNOT_BE_EMPTY, $details);

        self::assertNotNull($error);
        self::assertEquals("fieldName", $error->field());
        self::assertEquals(ErrorCode::FIELD_CANNOT_BE_EMPTY, $error->code());
        self::assertEquals($details, $error->details());
        self::assertEquals(
            'ValidationError[code = FIELD_CANNOT_BE_EMPTY, message = The field cannot be empty {field = fieldName, details = {"detailLabel":"detailDescription"}]',
            $error.''
        );
    }
}
