<?php

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Model\UploadedFile;

/**
 * @author Wilhelm Zwertvaegher
 */
interface UploadedFileStorageService
{
    public function upload(string $path, string $filename, string $type): UploadedFile;

    public function delete(UploadedFile $uploadedFile): void;

    public function generateUrl(UploadedFile $uploadedFile): string;
}
