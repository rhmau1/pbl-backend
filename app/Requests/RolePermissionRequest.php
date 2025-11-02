<?php

namespace App\Requests;

class RolePermissionRequest
{
    public static function validate(array $in): array
    {
        $roleId = isset($in['role_id']) ? (int)$in['role_id'] : null;
        $permission = isset($in['permission']) ? trim((string) $in['permission']) : null;

        $errors = [];

        if (!$roleId) {
            $errors['role_id'] = 'role_id is required';
        }

        if (!$permission) {
            $errors['permission'] = 'permission is required';
        }

        return [
            $errors === [],
            $errors,
            array_filter([
                'role_id' => $roleId,
                'permission' => $permission
            ], fn($v) => $v !== null)
        ];
    }
}
