<?php

namespace App\Shared\Domain\Port\Driven;

use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\TempFile;

/**
 * @author Wilhelm Zwertvaegher
 */
interface FileStorageService
{
    public function store(TempFile $tempFile, string $type): TempFile;

    public function delete(StoredFile $storedFile): void;

    public function generateUrl(StoredFile $storedFile): string;
}
