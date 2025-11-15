<?php

namespace App\Requests;

use Exception;

final class MitraPartnerRequest
{
    private const MAX_IMAGE_SIZE = 2 * 1024 * 1024;

    private const ALLOWED_IMAGE = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public static function validateCreate(array $file, array $in): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload failed");
        }

        $mime = mime_content_type($file['tmp_name']);
        $size = filesize($file['tmp_name']);

        if (!in_array($mime, self::ALLOWED_IMAGE, true)) {
            throw new Exception("Only JPG, PNG, WEBP are allowed");
        }

        if ($size > self::MAX_IMAGE_SIZE) {
            throw new Exception("Image cannot exceed 2MB");
        }

        $name        = isset($in['name']) ? trim((string)$in['name']) : '';
        $website_url = isset($in['website_url']) ? trim((string)$in['website_url']) : '';
        $position    = isset($in['position']) ? (int)$in['position'] : null;

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'name is required';
        }

        if ($website_url === '') {
            $errors['website_url'] = 'website_url is required';
        }

        if ($position === null) {
            $errors['position'] = 'position is required';
        }

        $payload = [
            'name'        => $name,
            'website_url' => $website_url,
            'position'    => $position
        ];

        return [
            $errors === [],
            $errors,
            $payload
        ];
    }

    public static function validateUpdate(?array $file, array $in): array
    {
        $errors = [];

        if ($file && $file['error'] === UPLOAD_ERR_OK) {

            $mime = mime_content_type($file['tmp_name']);
            $size = filesize($file['tmp_name']);

            if (!in_array($mime, self::ALLOWED_IMAGE, true)) {
                throw new Exception("Only JPG, PNG, WEBP are allowed");
            }

            if ($size > self::MAX_IMAGE_SIZE) {
                throw new Exception("Image cannot exceed 2MB");
            }
        }

        $name        = array_key_exists('name', $in) ? trim((string)$in['name']) : null;
        $website_url = array_key_exists('website_url', $in) ? trim((string)$in['website_url']) : null;
        $position    = array_key_exists('position', $in) ? (int)$in['position'] : null;

        if ($name !== null && $name === '') {
            $errors['name'] = 'name cannot be empty';
        }

        if ($website_url !== null && $website_url === '') {
            $errors['website_url'] = 'website_url cannot be empty';
        }

        if ($position !== null && $position <= 0) {
            $errors['position'] = 'position must be greater than 0';
        }

        $payload = [];
        if ($name !== null)        $payload['name'] = $name;
        if ($website_url !== null) $payload['website_url'] = $website_url;
        if ($position !== null)    $payload['position'] = $position;

        return [
            $errors === [],
            $errors,
            $payload
        ];
    }
}
