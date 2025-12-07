<?php

namespace App\Shared\Infrastructure\Adapter;

use App\Shared\Domain\Port\Driven\Clock;
use Symfony\Component\Clock\ClockAwareTrait;

/**
 * @author Wilhelm Zwertvaegher
 */
class ClockAdapter implements Clock
{

    use ClockAwareTrait;

    public function getNow(): \DateTimeImmutable
    {
        return $this->now();
    }
}
