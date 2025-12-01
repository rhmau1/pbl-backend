<?php

namespace App\Requests;

use Exception;

final class Media3DAssetUploadRequest
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
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // max 3D file 10MB
        if ($size > 10 * 1024 * 1024) {
            throw new Exception("3D file exceeds 10MB");
        }

        $allowedExtensions = ['glb', 'gltf', 'fbx', 'obj'];

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("File extension not allowed. Allowed: " . implode(', ', $allowedExtensions));
        }
    }
}
