<?php

namespace App\CollectionManagement\Domain\Repository;

use App\CollectionManagement\Domain\Model\LocalSet;
use App\CollectionManagement\Domain\Model\SetCollection;

interface LocalSetRepository
{
    public function add(LocalSet $localSet): void;

    public function update(LocalSet $localSet): void;
}
