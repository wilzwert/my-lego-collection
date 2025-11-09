<?php

namespace App\User\Application\Command;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class UpdateAvatarCommand
{

    public function __construct(
        public string $identityId,
        public string $filepath,
        public string $filename
    ) {
    }
}
