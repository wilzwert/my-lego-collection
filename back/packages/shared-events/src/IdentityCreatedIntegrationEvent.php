<?php

namespace MyLegoCollection\SharedEvent;

/**
 * @author Wilhelm Zwertvaegher
 */
class IdentityCreatedIntegrationEvent extends IntegrationEvent
{
    private string $id;

    public function __construct($type, string $id)
    {
        parent::__construct($type, ['id' => $id]);
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
