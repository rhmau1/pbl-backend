<?php

namespace App\Requests;

final class TimKreatifRequest
{
    public static function validateCreate(array $in, array $files): array
    {
        $name = isset($in['name']) ? trim((string) $in['name']) : '';
        $role = isset($in['role']) ? trim((string) $in['role']) : '';
        $position = isset($in['position']) ? (int) $in['position'] : 1;
        $skills = isset($in['skills']) ?  $in['skills'] : null;

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Name is required';
        }

        if ($role === '') {
            $errors['role'] = 'Role is required';
        }

        if ($position < 1) {
            $errors['position'] = 'Position must be at least 1';
        }
        if ($skills !== null && is_string($skills)) {
            $skills = json_decode($skills, true);
        }
        if ($skills !== null && !is_array($skills)) {
            $errors['skills'] = 'Skills must be an array';
        }
        $photo = null;
        if (isset($files['photo']) && $files['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($files['photo']['error'] !== 0) {
                $errors['photo'] = 'Photo upload error';
            } else {
                if ($files['photo']['size'] > 2 * 1024 * 1024) {
                    $errors['photo'] = 'Photo max 2MB';
                }

                $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($files['photo']['type'], $allowed)) {
                    $errors['photo'] = 'Invalid photo type';
                }

                $photo = $files['photo'];
            }
        }

        return [
            $errors === [],
            $errors,
            array_filter([
                'name' => $name,
                'role' => $role,
                'position' => $position,
                'skills' => $skills,
                'photo' => $photo,
            ], fn($v) => $v !== null)
        ];
    }

    public static function validateUpdate(array $in, array $files): array
    {
        $name = isset($in['name']) ? trim((string) $in['name']) : null;
        $role = isset($in['role']) ? trim((string) $in['role']) : null;
        $position = isset($in['position']) ? (int) $in['position'] : null;
        $skills = isset($in['skills']) ? $in['skills'] : null;

        $errors = [];

        if ($name !== null && $name === '') {
            $errors['name'] = 'Name cannot be empty';
        }

        if ($role !== null && $role === '') {
            $errors['role'] = 'Role cannot be empty';
        }

        if ($position !== null && $position < 1) {
            $errors['position'] = 'Position must be at least 1';
        }
        if ($skills !== null && is_string($skills)) {
            $skills = json_decode($skills, true);
        }
        if ($skills !== null && !is_array($skills)) {
            $errors['skills'] = 'Skills must be an array';
        }
        $photo = null;
        if (isset($files['photo']) && $files['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($files['photo']['error'] !== 0) {
                $errors['photo'] = 'Photo upload error';
            } else {
                if ($files['photo']['size'] > 2 * 1024 * 1024) {
                    $errors['photo'] = 'Photo max 2MB';
                }

                $allowed = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($files['photo']['type'], $allowed)) {
                    $errors['photo'] = 'Invalid photo type';
                }

                $photo = $files['photo'];
            }
        }

        return [
            $errors === [],
            $errors,
            array_filter([
                'name' => $name,
                'role' => $role,
                'position' => $position,
                'skills' => $skills,
                'photo' => $photo,
            ], fn($v) => $v !== null)
        ];
    }
}
