<?php

namespace App\Requests;

final class RoleRequest
{
    public static function validate(array $in): array
    {
        $name = isset($in['name']) ? trim((string) $in['name']) : null;

        $errors = [];

        if ($name === null) {
            $errors['name'] = 'Name is required';
        }
        if ($name !== null) {
            if ($name === '') {
                $errors['name'] = 'Name cannot be empty';
            } elseif (strlen($name) < 2) {
                $errors['name'] = 'Name must be at least 2 characters';
            }
        }

        return [
            $errors === [],
            $errors,
            array_filter(['name' => $name], fn($v) => $v !== null)
        ];
    }
}
