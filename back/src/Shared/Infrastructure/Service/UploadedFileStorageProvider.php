<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Model\UploadedFile;

/**
 * @author Wilhelm Zwertvaegher
 */
interface UploadedFileStorageProvider
{
    public function upload(string $path, string $filename, string $type): UploadedFile;

    public function delete(UploadedFile $uploadedFile): void;

    public function generateUrl(UploadedFile $uploadedFile): string;

    /**
     * @return array
     */
    public function supports(string $type): bool;
}
