<?php

namespace App\CollectionManagement\Domain\Repository;

use App\CollectionManagement\Domain\Model\Local\Set;

interface LocalSetRepository
{
    public function add(Set $localSet): void;

    public function update(Set $localSet): void;
}
