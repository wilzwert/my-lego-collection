<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Model\File;

/**
 * @author Wilhelm Zwertvaegher
 */
interface UploadedFileStorageProvider
{
    public function upload(string $path, string $filename, string $type): File;

    public function delete(File $uploadedFile): void;

    public function generateUrl(File $uploadedFile): string;

    /**
     * @return array
     */
    public function supports(string $type): bool;
}
