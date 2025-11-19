<?php

namespace App\Tests\Shared\Domain\Validation;

use App\Shared\Domain\Exception\ErrorCode;
use App\Shared\Domain\Validation\ValidationError;
use App\Shared\Domain\Validation\ValidationErrors;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @author Wilhelm Zwertvaegher
 */

class ValidationErrorsTest extends TestCase
{
    #[Test]
    public function shouldAddValidationError(): void
    {
        $validationErrors = new ValidationErrors();
        self::assertCount(0, $validationErrors->getErrors());

        $validationErrors->add(new ValidationError("field", ErrorCode::UNKNOWN_ERROR));
        self::assertCount(1, $validationErrors->getErrors());

        $firstError = array_values($validationErrors->getErrors()['field'])[0];
        self::assertEquals(ErrorCode::UNKNOWN_ERROR->getCode(), $firstError->code()->getCode());
    }

    #[Test]
    public function shouldAddValidationErrors(): void
    {
        $errors = new ValidationErrors();
        self::assertCount(0, $errors->getErrors());

        $errors->add(new ValidationError("field", ErrorCode::FIELD_CANNOT_BE_EMPTY));
        $errors->add(new ValidationError("field", ErrorCode::UNKNOWN_ERROR));
        $errors->add(new ValidationError("field2", ErrorCode::FIELD_VALUE_TOO_BIG));
        $errors->add(new ValidationError("field2", ErrorCode::UNKNOWN_ERROR));
        $errors->add(new ValidationError("field3", ErrorCode::INVALID_EMAIL));

        self::assertCount(3, $errors->getErrors());
        $fieldErrors = $errors->getErrors()["field"];
        self::assertCount(2, $fieldErrors);
        self::assertEquals(ErrorCode::FIELD_CANNOT_BE_EMPTY, $fieldErrors['FIELD_CANNOT_BE_EMPTY']->code());
        self::assertEquals(ErrorCode::UNKNOWN_ERROR, $fieldErrors['UNKNOWN_ERROR']->code());

        $field2Errors = $errors->getErrors()["field2"];
        self::assertCount(2, $field2Errors);
        self::assertEquals(ErrorCode::FIELD_VALUE_TOO_BIG, $field2Errors['FIELD_VALUE_TOO_BIG']->code());
        self::assertEquals(ErrorCode::UNKNOWN_ERROR, $field2Errors['UNKNOWN_ERROR']->code());

        self::assertEquals(ErrorCode::INVALID_EMAIL, $errors->getErrors()["field3"]['INVALID_EMAIL']->code());
    }


    #[Test]
    public function shouldMergeValidationErrors(): void {
        $errors = new ValidationErrors();
        $errors->add(new ValidationError("field", ErrorCode::FIELD_CANNOT_BE_EMPTY));
        $errors->add(new ValidationError("field", ErrorCode::ENTITY_NOT_FOUND));
        $errors->add(new ValidationError("field2", ErrorCode::FIELD_VALUE_TOO_BIG));
        $errors->add(new ValidationError("field2", ErrorCode::UNKNOWN_ERROR, ["unknownDetailKey" => "unknownDetailMessage"]));
        $errors->add(new ValidationError("field3", ErrorCode::ENTITY_NOT_FOUND));

        $errors2  = new ValidationErrors();
        $errors2->add(new ValidationError("field", ErrorCode::INVALID_EMAIL));
        // this one is a duplicate and should not be added
        $errors2->add(new ValidationError("field", ErrorCode::ENTITY_NOT_FOUND));
        // this one is a duplicate and should replace the other one because it has details
        $errors2->add(new ValidationError("field2", ErrorCode::FIELD_VALUE_TOO_BIG, ["valueTooBigDetailKey" => "valueTooBigDetailMessage"]));
        $errors2->add(new ValidationError("field2", ErrorCode::FIELD_VALUE_TOO_BIG, ["secondValueTooBigDetailKey" => "secondValueTooBigDetailMessage"]));

        $errors2->add(new ValidationError("field4", ErrorCode::UNKNOWN_ERROR));

        // when
        $errors->merge($errors2);

        // then
        self::assertCount(4, $errors->getErrors());
        $fieldErrors = array_values($errors->getErrors()["field"]);
        self::assertCount(3, $fieldErrors);
        // errors should have been merged, keeping the first ValidationErrors order
        self::assertEquals(ErrorCode::FIELD_CANNOT_BE_EMPTY, $fieldErrors[0]->code());
        self::assertEquals(ErrorCode::ENTITY_NOT_FOUND, $fieldErrors[1]->code());
        self::assertEquals(ErrorCode::INVALID_EMAIL, $fieldErrors[2]->code());

        $field2Errors = array_values($errors->getErrors()["field2"]);
        self::assertCount(2, $field2Errors);

        self::assertEquals(ErrorCode::FIELD_VALUE_TOO_BIG, $field2Errors[0]->code());
        // details of the second ValidationErrors should have been added
        self::assertNotNull($field2Errors[0]->details());

        $valueTooBigDetails = $field2Errors[0]->details();
        self::assertCount(2, $valueTooBigDetails);
        self::assertEquals("valueTooBigDetailMessage", $valueTooBigDetails["valueTooBigDetailKey"]);
        self::assertEquals("secondValueTooBigDetailMessage", $valueTooBigDetails["secondValueTooBigDetailKey"]);

        // details of the UNEXPECTED_ERROR ValidationErrors should have been added
        self::assertNotNull($field2Errors[1]->details());
        self::assertEquals(ErrorCode::UNKNOWN_ERROR, $field2Errors[1]->code());
        self::assertEquals("unknownDetailMessage", $field2Errors[1]->details()["unknownDetailKey"]);


        self::assertEquals(ErrorCode::ENTITY_NOT_FOUND, $errors->getErrors()["field3"]['ENTITY_NOT_FOUND']->code());

        self::assertCount(1, $errors->getErrors()["field4"]);
    }
}
