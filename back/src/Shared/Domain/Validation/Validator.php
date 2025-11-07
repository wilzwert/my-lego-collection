<?php

namespace App\Shared\Domain\Validation;

use App\Shared\Domain\Exception\BaseErrorCode;
use App\Shared\Domain\Exception\ErrorCode;
use App\Shared\Domain\Exception\ValidationException;
use App\Shared\Domain\Model\Uuid;

/**
 * @author Wilhelm Zwertvaegher
 */

/**
 * @author Wilhelm Zwertvaegher
 */

class Validator
{

    public ValidationErrors $validationErrors;

    /**
     * @return ValidationErrors
     */
    public function getValidationErrors(): ValidationErrors
    {
        return $this->validationErrors;
    }

    /**
     *
     */
    public function __construct()
    {
        $this->validationErrors = new ValidationErrors();
    }

    /**
     * @param string $fieldName
     * @param mixed $fieldValue
     * @return bool
     */
    private function notEmpty(string $fieldName, mixed $fieldValue): bool
    {
        if ($fieldValue === null
            || $fieldValue === ''
            || $fieldValue instanceof Uuid && $fieldValue->value() === null) {
            $this->validationErrors->add(new ValidationError($fieldName, ErrorCode::FIELD_CANNOT_BE_EMPTY, ['empty' => 'Field cannot be empty']));
            return false;
        }
        return true;
    }

    /**
     * @throws ValidationException
     */
    public function validate(): void
    {
        if ($this->validationErrors->hasErrors()) {
            throw new ValidationException($this->validationErrors);
        }
    }

    /**
     * @param string $fieldName
     * @param mixed $fieldValue
     * @return $this
     */
    public function requireNotNull(string $fieldName, mixed $fieldValue): self
    {
        if (null === $fieldValue) {
            $this->validationErrors->add(new ValidationError($fieldName, ErrorCode::FIELD_CANNOT_BE_NULL));
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param mixed $fieldValue
     * @return $this
     */
    public function requireNotEmpty(string $fieldName, mixed $fieldValue): self
    {
        $this->notEmpty($fieldName, $fieldValue);
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $fieldValue
     * @return $this
     */
    public function requireValidEmail(string $fieldName, string $fieldValue): self
    {
        $pattern = "/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$/";
        if ($this->notEmpty($fieldName, $fieldValue) && !preg_match($pattern, $fieldValue)) {
            $this->validationErrors->add(new ValidationError($fieldName, ErrorCode::INVALID_EMAIL, ['invalid' => 'Invalid email']));
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $fieldValue
     * @return $this
     */
    public function requireValidUrl(string $fieldName, string $fieldValue): self
    {
        try {
            $parts = parse_url($fieldValue);
            if (!is_array($parts)) {
                throw new \Exception('Malformed URL');
            }

            if (empty($parts['scheme']) || !str_starts_with($parts['scheme'], 'http')) {
                throw new \Exception('URL must start with http(s).');
            }

            if (empty($parts['host'])) {
                throw new \Exception('URL must contain a host.');
            }

            if (!str_contains($parts['host'], '.')) {
                throw new \Exception('URL must contain a valid host.');
            }

        } catch (\Exception $e) {
            $this->validationErrors->add(new ValidationError($fieldName, ErrorCode::INVALID_URL, ['invalid' => $e->getMessage()]));
        }

        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $fieldValue
     * @param int $minLength
     * @return $this
     */
    public function requireMinLength(string $fieldName, string $fieldValue, int $minLength): self
    {
        if ($minLength < 1) {
            throw new \InvalidArgumentException("minLength must be greater than 0");
        }
        if ($this->notEmpty($fieldName, $fieldValue) && strlen($fieldValue) < $minLength) {
            $this->validationErrors->add(new ValidationError($fieldName, ErrorCode::FIELD_VALUE_TOO_SHORT));
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param string $fieldValue
     * @param int $maxLength
     * @return $this
     */
    public function requireMaxLength(string $fieldName, string $fieldValue, int $maxLength): self
    {
        if ($maxLength < 1) {
            throw new \InvalidArgumentException("maxLength must be greater than 0");
        }
        if ($this->notEmpty($fieldName, $fieldValue) && strlen($fieldValue) > $maxLength) {
            $this->validationErrors->add(new ValidationError($fieldName, ErrorCode::FIELD_VALUE_TOO_LONG));
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param int $fieldValue
     * @param int $minValue
     * @return $this
     */
    public function requireMin(string $fieldName, int $fieldValue, int $minValue): self
    {
        if ($this->notEmpty($fieldName, $fieldValue) && $fieldValue < $minValue) {
            $this->validationErrors->add(new ValidationError($fieldName, ErrorCode::FIELD_VALUE_TOO_SMALL, ["min" => $minValue]));
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param int $fieldValue
     * @param int $minValue
     * @return $this
     */
    public function requireMinIfNotNull(string $fieldName, int $fieldValue, int $minValue): self
    {
        if ($fieldValue < $minValue) {
            $this->validationErrors->add(new ValidationError($fieldName, ErrorCode::FIELD_VALUE_TOO_SMALL, ["min" => $minValue]));
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param int $fieldValue
     * @param int $maxValue
     * @return $this
     */
    public function requireMax(string $fieldName, int $fieldValue, int $maxValue): self
    {
        if ($this->notEmpty($fieldName, $fieldValue) && $fieldValue > $maxValue) {
            $this->validationErrors->add(new ValidationError($fieldName, ErrorCode::FIELD_VALUE_TOO_BIG, ["max" => $maxValue]));
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param int $fieldValue
     * @param int $maxValue
     * @return $this
     */
    public function requireMaxIfNotNull(string $fieldName, int $fieldValue, int $maxValue): self
    {
        if ($fieldValue > $maxValue) {
            $this->validationErrors->add(new ValidationError($fieldName, ErrorCode::FIELD_VALUE_TOO_BIG, ["max" => $maxValue]));
        }
        return $this;
    }

    /**
     * @param string $fieldName
     * @param callable():bool $supplier
     * @param BaseErrorCode $errorCode
     * @return $this
     */
    public function require(string $fieldName, callable $supplier, BaseErrorCode $errorCode): self
    {
        if (true !== $supplier()) {
            $this->validationErrors->add(new ValidationError($fieldName, $errorCode));
        }
        return $this;
    }
}
