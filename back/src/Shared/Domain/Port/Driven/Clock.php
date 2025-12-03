<?php

namespace App\Shared\Domain\Port\Driven;

/**
 * @author Wilhelm Zwertvaegher
 */
interface Clock
{
    public function getNow(): \DateTimeImmutable;
}
