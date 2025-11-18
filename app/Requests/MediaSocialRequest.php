<?php

namespace App\Requests;

use Exception;

final class MediaSocialRequest
{

    public static function validateCreate(array $in): array
    {
        $platform = isset($in['platform']) ? trim((string)$in['platform']) : '';
        $url = isset($in['url']) ? trim((string) $in['url']) : '';
        $icon = isset($in['icon']) ? trim((string) $in['icon']) : '';
        $position = isset($in['position']) ? (int)$in['position'] : null;
        $errors = [];
        if ($platform === '') $errors['platform'] = 'platform required';
        if ($url === '') $errors['url'] = 'url required';
        if ($icon === '') $errors['icon'] = 'icon required';
        if ($position === null) $errors['position'] = 'position required';
        $payload = array_filter([
            'platform' => $platform,
            'url' => $url,
            'icon' => $icon,
            'position' => $position
        ], fn($v) => $v !== null);


        return [$errors === [], $errors, $payload];
    }
    public static function validateUpdate(array $in): array
    {
        $platform = isset($in['platform']) ? trim((string) $in['platform']) : null;
        $url = isset($in['url']) ? trim((string) $in['url']) : null;
        $icon = isset($in['icon']) ? trim((string) $in['icon']) : null;
        $position = isset($in['position']) ? (int) $in['position'] : null;
        $errors = [];

        if ($platform !== null && $platform === '')
            $errors['platform'] = 'platform required if provided';
        if ($url !== null && $url === '')
            $errors['url'] = 'Url required if provided';
        if ($icon !== null && $icon === '')
            $errors['icon'] = 'icon required if provided';
        if ($position !== null && $position <= 0)
            $errors['position'] = 'position required if provided';

        return [
            $errors === [],
            $errors,
            array_filter(['platform' => $platform, 'url' => $url, 'icon' => $icon, 'position' => $position], fn($v) => $v !== null)
        ];
    }
}
