<?php

namespace App\CollectionManagement\Infrastructure\Persistence\Doctrine\Entity;

use App\CollectionManagement\Domain\Model\Local\UserSet;
use App\Shared\Domain\Model\EntityId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_sets")
 */
class DoctrineUserSet
{
    #[ORM\Id, ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 36)]
    private string $userId;

    private DoctrineSet $set;

    public function __construct(string $id, string $userId, DoctrineSet $set)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->set = $set;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toDomain(): UserSet
    {
        return new UserSet(EntityId::fromString($this->id), EntityId::fromString($this->userId), $this->set->toDomain());
    }
}
