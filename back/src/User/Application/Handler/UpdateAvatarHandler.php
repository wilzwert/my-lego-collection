<?php

namespace App\User\Application\Handler;

use App\User\Application\Command\UpdateAvatarCommand;
use App\User\Domain\Service\UserService;

readonly class UpdateAvatarHandler
{
    public function __construct(
        private readonly UserService                $userService,
    ){
    }

    public function __invoke(UpdateAvatarCommand $command): void
    {
        $this->userService->updateAvatar($command);
    }
}
