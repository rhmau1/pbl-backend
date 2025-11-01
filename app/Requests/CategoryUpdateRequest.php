<?php

namespace App\Requests;

final class CategoryUpdateRequest
{
    public static function validate(array $in): array
    {
        $name = isset($in['name']) ? trim((string) $in['name']) : null;
        $type = isset($in['type']) ? trim((string) $in['type']) : null;

        $errors = [];

        if ($name === null || $name === '')
            $errors['name'] = 'Name is required';

        if ($type === null || $type === '')
            $errors['type'] = 'Type is required';

        return [
            $errors === [],
            $errors,
            array_filter(['name' => $name, 'type' => $type], fn($v) => $v !== null)
        ];
    }
}
