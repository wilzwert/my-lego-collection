<?php

namespace App\Tests\Shared\Domain\Validation;

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

        $this->assertNotNull($error);
        $this->assertEquals("fieldName", $error->field());
        $this->assertEquals(ErrorCode::FIELD_CANNOT_BE_EMPTY, $error->code());
        $this->assertEquals($details, $error->details());
        $this->assertEquals(
            'ValidationError[code = FIELD_CANNOT_BE_EMPTY, message = The field cannot be empty {field = fieldName, details = {"detailLabel":"detailDescription"}]',
            $error.''
        );
    }
}
