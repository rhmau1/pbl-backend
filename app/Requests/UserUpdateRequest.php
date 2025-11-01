<?php
namespace App\Requests;

final class UserUpdateRequest
{
    public static function validate(array $in): array
    {
        $name = isset($in['name']) ? trim((string) $in['name']) : null;
        $password = isset($in['password']) ? (string) $in['password'] : null;
        $errors = [];
        if ($name !== null && $name === '')
            $errors['name'] = 'Name required if provided';
        if ($password !== null && strlen($password) < 6)
            $errors['password'] = 'Min 6 chars';
        return [$errors === [], $errors, array_filter(['name' => $name, 'password' => $password], fn($v) => $v !== null)];
    }
}
