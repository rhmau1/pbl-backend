<?php

namespace App\Requests;

use Exception;

final class Media3DAssetUploadRequest
{
    public static function validate(string $url, int $visibility): void
    {
        if (!in_array($visibility, [0, 1])) {
            throw new Exception("Invalid visibility (0 or 1 only)");
        }

        if (empty($url)) {
            throw new Exception("URL is required");
        }

        // Allow relative paths, so we skip FILTER_VALIDATE_URL
        // if (!filter_var($url, FILTER_VALIDATE_URL)) {
        //    throw new Exception("Invalid URL format");
        // }

        $path = parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $allowedExtensions = ['glb', 'gltf', 'fbx', 'obj'];

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("File extension not allowed. Allowed: " . implode(', ', $allowedExtensions));
        }
    }
}
