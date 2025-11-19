<?php

namespace App\User\Application\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class DeleteAvatarCommand
{
    public function __construct(public string $identityId)
    {

    }
}
