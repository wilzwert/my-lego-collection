<?php

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Model\UploadedFile;

/**
 * @author Wilhelm Zwertvaegher
 */
interface UploadedFileStorage
{
    public function upload(string $path, string $filename): void;

    public function generateUrl(UploadedFile $uploadedFile): string;
}
