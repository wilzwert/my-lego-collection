<?php

namespace App\Shared\Domain\Port\Driven;

use App\Shared\Domain\Model\EntityId;
use App\Shared\Domain\Model\StoredFile;

interface StoredFileRepository
{
    public function findById(EntityId $id): ?StoredFile;

    public function save(StoredFile $storedFile): void;
    public function delete(StoredFile $storedFile): void;
}
