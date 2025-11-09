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
    public static function validateProfile(array $in, array $files): array
    {
        $skills  = $in['skills'] ?? null;
        $socials = $in['socials'] ?? null;

        $errors = [];

        if ($skills !== null && is_string($skills)) {
            $skills = json_decode($skills, true);
        }
        if ($skills !== null && !is_array($skills)) {
            $errors['skills'] = 'Skills must be an array';
        }

        if ($socials !== null && is_string($socials)) {
            $socials = json_decode($socials, true);
        }

        if ($socials !== null && !is_array($socials)) {
            $errors['socials'] = 'Socials must be an object';
        }
        if (isset($files['avatar']) && $files['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {

            if ($files['avatar']['error'] !== 0) {
                $errors['avatar'] = 'Avatar upload error';
            }

            if ($files['avatar']['size'] > 2 * 1024 * 1024) {
                $errors['avatar'] = 'Avatar max 2MB';
            }

            $allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($files['avatar']['type'], $allowed)) {
                $errors['avatar'] = 'Invalid avatar type';
            }
        }

        return [
            $errors === [],
            $errors,
            array_filter([
                'skills'  => $skills,
                'socials' => $socials,
            ], fn($v) => $v !== null)
        ];
    }
}
