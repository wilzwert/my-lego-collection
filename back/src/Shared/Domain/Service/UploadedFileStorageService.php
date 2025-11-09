<?php

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Model\File;

/**
 * @author Wilhelm Zwertvaegher
 */
interface UploadedFileStorageService
{
    public function upload(string $path, string $filename, string $type): File;

    public function delete(File $uploadedFile): void;

    public function generateUrl(File $uploadedFile): string;
}
