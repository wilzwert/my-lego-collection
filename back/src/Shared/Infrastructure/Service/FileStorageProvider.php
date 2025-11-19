<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\TempFile;

/**
 * @author Wilhelm Zwertvaegher
 */
interface FileStorageProvider
{
    public function store(TempFile $tempFile, string $type): TempFile;

    public function delete(StoredFile $storedFile): void;

    public function generateUrl(StoredFile $storedFile): string;

    /**
     * @return array
     */
    public function supports(string $type): bool;
}
