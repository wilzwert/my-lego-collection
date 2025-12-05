<?php

namespace App\Auth\Infrastructure\Adapter;

use App\Auth\Domain\Model\Identity;
use App\Auth\Domain\Port\Driven\IdentityRepository;
use App\Notification\Application\Port\Driven\RetrieveIdentityDto;
use App\Notification\Domain\Model\IdentityInfo;
use App\Shared\Domain\Model\EntityId;
use MyLegoCollection\SharedContracts\Dto\IdentityDto;

/**
 * @author Wilhelm Zwertvaegher
 */
readonly class RetrieveIdentityDtoAdapter implements RetrieveIdentityDto
{
    public function __construct(private IdentityRepository $identityRepository)
    {
    }

    private function map(?Identity $identity): ?IdentityDto
    {
        return null === $identity ? null : new IdentityDto($identity->getId(), $identity->getEmail(), $identity->getUsername());
    }

    public function getIdentityDtoFromId(string $identityId): ?IdentityDto
    {
        return $this->map($this->identityRepository->findById(EntityId::fromString($identityId)));
    }
}
