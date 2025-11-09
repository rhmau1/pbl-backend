<?php

namespace App\Requests;

use Exception;

final class MediaAssetUploadRequest
{
    public static function validate(array $file, int $visibility): void
    {
        if (!in_array($visibility, [0, 1])) {
            throw new Exception("Invalid visibility (0 or 1 only)");
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Upload error");
        }

        $mime = mime_content_type($file['tmp_name']);
        $size = filesize($file['tmp_name']);

        // max image 2MB
        if (str_contains($mime, 'image/') && $size > 2 * 1024 * 1024) {
            throw new Exception("Image exceeds 2MB");
        }
        if (str_contains($mime, 'video/') && $size > 2 * 1024 * 1024) {
            throw new Exception("Video exceeds 2MB");
        }

        $allowed = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'video/mp4',
            'video/webm',
            'video/quicktime',
            'image/gif' // animasi gif
        ];

        if (!in_array($mime, $allowed)) {
            throw new Exception("File type not allowed");
        }
    }
}
