<?php

namespace App\Requests;

use Exception;

final class SejarahRequest
{

    public static function validateCreate(array $in): array
    {
        $title = isset($in['title']) ? trim((string)$in['title']) : '';
        $content = isset($in['content']) ? trim((string)$in['content']) : '';

        $errors = [];
        if ($content === '') $errors['content'] = 'content required';
        if ($title === '') $errors['title'] = 'title required';
        $payload = array_filter([
            'content' => $content,
            'title' => $title,
        ], fn($v) => $v !== null);


        return [$errors === [], $errors, $payload];
    }
    public static function validateUpdate(array $in): array
    {
        $content = isset($in['content']) ? trim((string) $in['content']) : null;
        $title = isset($in['title']) ? trim((string) $in['title']) : null;

        $errors = [];

        if ($content !== null && $content === '')
            $errors['content'] = 'content required if provided';

        if ($title !== null && $title === '')
            $errors['title'] = 'title required if provided';

        return [
            $errors === [],
            $errors,
            array_filter(['content' => $content, 'title' => $title], fn($v) => $v !== null)
        ];
    }
}
