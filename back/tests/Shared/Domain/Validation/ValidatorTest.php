<?php

namespace App\Tests\Shared\Domain\Validation;

use App\Shared\Domain\Exception\ErrorCode;
use App\Shared\Domain\Validation\Validator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

class ValidatorTest extends TestCase
{

    #[Test]
    public function whenEmpty_thenShouldAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireNotEmpty("field", "");
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::FIELD_CANNOT_BE_EMPTY, $validator->getValidationErrors()->getErrors()["field"]['FIELD_CANNOT_BE_EMPTY']->code());
    }

    #[Test]
    public function whenNotEmpty_thenShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireNotEmpty("field", "not empty");
        $this->assertCount(0, $validator->getValidationErrors()->getErrors());
    }

    #[Test]
    public function whenInvalidEmail_thenShouldAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireValidEmail("field", "invalid");
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::INVALID_EMAIL, $validator->getValidationErrors()->getErrors()["field"]['INVALID_EMAIL']->code());
    }
    #[Test]
    public function whenValidEmail_thenShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireValidEmail("field", "email@example.com");
        $this->assertCount(0, $validator->getValidationErrors()->getErrors());
    }

    #[Test]
    public function whenInvalidScheme_thenShouldAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireValidUrl("field", "htp://invalid");
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::INVALID_URL, $validator->getValidationErrors()->getErrors()["field"]['INVALID_URL']->code());
    }

    #[Test]
    public function whenInvalidHost_thenShouldAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireValidUrl("field", "http://invalid");
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::INVALID_URL, $validator->getValidationErrors()->getErrors()["field"]['INVALID_URL']->code());
    }

    #[Test]
    public function whenValidUrl_thenShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireValidUrl("field", "https://example.com");
        $this->assertCount(0, $validator->getValidationErrors()->getErrors());
    }

    #[Test]
    public function whenMinLengthLessThan1_thenShouldThrowAssertionError(): void
    {
        $validator = new Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->requireMinLength("field", "value", 0);
    }

    #[Test]
    public function whenMinLengthNotMet_thenShouldAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMinLength("field", "value", 6);
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::FIELD_VALUE_TOO_SHORT, $validator->getValidationErrors()->getErrors()["field"]['FIELD_VALUE_TOO_SHORT']->code());
    }

    #[Test]
    public function whenMinLengthMet_thenShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMinLength("field", "value", 4);
        $this->assertCount(0, $validator->getValidationErrors()->getErrors());
    }

    #[Test]
    public function whenMaxLengthLessThan1_thenShouldThrowAssertionError(): void
    {
        $validator = new Validator();
        $this->expectException(\InvalidArgumentException::class);
        $validator->requireMaxLength("field", "value", 0);
    }

    #[Test]
    public function whenMaxLengthNotMet_thenShouldAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMaxLength("field", "value", 2);
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::FIELD_VALUE_TOO_LONG, $validator->getValidationErrors()->getErrors()["field"]['FIELD_VALUE_TOO_LONG']->code());
    }

    #[Test]
    public function whenMaxLengthMet_thenShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMaxLength("field", "value", 6);
        $this->assertCount(0, $validator->getValidationErrors()->getErrors());
    }

    #[Test]
    public function whenMaxNotMet_thenShouldAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMax("field", 3, 2);
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::FIELD_VALUE_TOO_BIG, $validator->getValidationErrors()->getErrors()["field"]['FIELD_VALUE_TOO_BIG']->code());
    }

    #[Test]
    public function whenMaxMet_thenShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMax("field", 3, 3);
        $this->assertCount(0, $validator->getValidationErrors()->getErrors());
    }

    #[Test]
    public function whenMinNotMet_thenShouldAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMin("field", 2, 3);
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::FIELD_VALUE_TOO_SMALL, $validator->getValidationErrors()->getErrors()["field"]['FIELD_VALUE_TOO_SMALL']->code());
    }

    #[Test]
    public function whenMinMet_thenShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMin("field", 7, 6);
        $this->assertCount(0, $validator->getValidationErrors()->getErrors());
    }

    #[Test]
    public function whenValueIsNull_thenMinIfNotNullShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $this->expectException(\TypeError::class);
        $validator->requireMinIfNotNull("field", null, 2);
    }

    #[Test]
    public function whenMinMet_thenMinIfNotNullShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMinIfNotNull("field", 6, 6);
        $this->assertCount(0, $validator->getValidationErrors()->getErrors());
    }

    #[Test]
    public function whenMinNotMet_thenMinIfNotNullShouldAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMinIfNotNull("field", 6, 7);
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::FIELD_VALUE_TOO_SMALL, $validator->getValidationErrors()->getErrors()["field"]['FIELD_VALUE_TOO_SMALL']->code());
    }

    #[Test]
    public function whenValueIsNull_thenMaxIfNotNullShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $this->expectException(\TypeError::class);
        $validator->requireMaxIfNotNull("field", null, 2);
    }

    #[Test]
    public function whenMaxMet_thenMaxIfNotNullShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMaxIfNotNull("field", 6, 6);
        $this->assertCount(0, $validator->getValidationErrors()->getErrors());
    }

    #[Test]
    public function whenMaxNotMet_thenMaxIfNotNullShouldAddValidationError(): void
    {
        $validator = new Validator();
        $validator->requireMaxIfNotNull("field", 7, 6);
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::FIELD_VALUE_TOO_BIG, $validator->getValidationErrors()->getErrors()["field"]['FIELD_VALUE_TOO_BIG']->code());
    }


    #[Test]
    public function whenRequireSupplierIsFalse_thenShouldAddValidationErrorWithProvidedErrorCode(): void
    {
        $validator = new Validator();
        $validator->require("field", fn () => false, ErrorCode::UNKNOWN_ERROR);
        $this->assertCount(1, $validator->getValidationErrors()->getErrors());
        $this->assertEquals(ErrorCode::UNKNOWN_ERROR, $validator->getValidationErrors()->getErrors()["field"]['UNKNOWN_ERROR']->code());
    }

    #[Test]

    public function whenRequireSupplierIsTrue_thenShouldNotAddValidationError(): void
    {
        $validator = new Validator();
        $validator->require("field", fn () => true, ErrorCode::UNKNOWN_ERROR);
        $this->assertCount(0, $validator->getValidationErrors()->getErrors());
    }
}
