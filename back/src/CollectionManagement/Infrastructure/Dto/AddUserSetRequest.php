<?php

namespace App\CollectionManagement\Infrastructure\Dto;

/**
 * @author Wilhelm Zwertvaegher
 */

class AddUserSetRequest
{
    public function __construct(
        private readonly string $externalSetId
    ) {
    }

    public function getExternalSetId(): string
    {
        return $this->externalSetId;
    }
}
