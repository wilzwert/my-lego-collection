<?php

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Model\StoredFile;
use App\Shared\Domain\Model\TempFile;

/**
 * @author Wilhelm Zwertvaegher
 */
interface StoredFileService
{
    public function replace(?StoredFile $storedFile, TempFile $tempFile, string $fileType): StoredFile;
    public function delete(StoredFile $storedFile): void;
}
