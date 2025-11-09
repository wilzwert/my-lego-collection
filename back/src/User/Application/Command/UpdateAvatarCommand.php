<?php

namespace App\User\Application\Command;

use App\Shared\Domain\Model\TempFile;

/**
 * @author Wilhelm Zwertvaegher
 */
final readonly class UpdateAvatarCommand
{

    public function __construct(
        public string $identityId,
        public TempFile $tempFile
    ) {
    }
}
