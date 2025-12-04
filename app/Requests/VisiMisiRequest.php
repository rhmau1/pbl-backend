<?php

namespace App\Requests;

use Exception;

final class VisiMisiRequest
{

    public static function validateCreate(array $in): array
    {
        $content = isset($in['content']) ? trim((string)$in['content']) : '';
        $type = isset($in['type']) ? trim((string) $in['type']) : '';
        if (!in_array($type, ["visi", "misi", "sejarah"])) {
            throw new Exception("Invalid type (visi, misi, sejarah only)");
        }
        $position = isset($in['position']) ? (int)$in['position'] : null;
        $is_active = isset($in['is_active']) ? (int) $in['is_active'] : 1;

        $errors = [];
        if ($content === '') $errors['content'] = 'content required';
        if ($type === '') $errors['type'] = 'type required';
        if ($position === null) $errors['position'] = 'position required';

        $payload = array_filter([
            'content' => $content,
            'type' => $type,
            'position' => $position,
            'is_active' => $is_active,
        ], fn($v) => $v !== null);


        return [$errors === [], $errors, $payload];
    }
    public static function validateUpdate(array $in): array
    {
        $content = isset($in['content']) ? trim((string) $in['content']) : null;
        $type = isset($in['type']) ? trim((string) $in['type']) : null;
        $is_active = isset($in['is_active']) ? (int) $in['is_active'] : null;

        if (!in_array($type, ["visi", "misi", "sejarah"])) {
            throw new Exception("Invalid type (visi, misi, sejarah only)");
        }
        $position = isset($in['position']) ? (int) $in['position'] : null;

        $errors = [];

        if ($content !== null && $content === '')
            $errors['content'] = 'content required if provided';

        if ($type !== null && $type === '')
            $errors['type'] = 'Type required if provided';
        if ($position !== null && $position <= 0)
            $errors['position'] = 'position required if provided';
        if ($is_active !== null && !in_array($is_active, [0, 1]))
            $errors['is_active'] = 'Is active must be 0 or 1';
        return [
            $errors === [],
            $errors,
            array_filter(['content' => $content, 'type' => $type, 'position' => $position, 'is_active' => $is_active], fn($v) => $v !== null)
        ];
    }
}
