<?php

namespace App\Shared\Domain\Model;

use App\Shared\Domain\Event\DomainEvent;

/**
 * Interface to explicitly mark an aggregate as able to generate DomainEvents
 * @author Wilhelm Zwertvaegher
 */
interface ProducesDomainEvents
{
    /**
     * @return array<DomainEvent>
     */
    public function pullEvents(): array;
}
