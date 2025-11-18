<?php

namespace App\Requests;

final class FooterSectionRequest
{
    public static function validateCreate(array $in): array
    {
        $section_name = isset($in['section_name']) ? trim((string) $in['section_name']) : null;
        $position = isset($in['position']) ? (int)$in['position'] : null;

        $errors = [];

        if ($section_name === null) {
            $errors['section_name'] = 'Section_name is required';
        }
        if ($section_name !== null) {
            if ($section_name === '') {
                $errors['section_name'] = 'Section_name cannot be empty';
            } elseif (strlen($section_name) < 2) {
                $errors['section_name'] = 'Section_name must be at least 2 characters';
            }
        }
        if ($position === null) $errors['position'] = 'position required';

        return [
            $errors === [],
            $errors,
            array_filter([
                'section_name' => $section_name,
                'position' => $position
            ], fn($v) => $v !== null)
        ];
    }
    public static function validateUpdate(array $in): array
    {
        $section_name = isset($in['section_name']) ? trim((string) $in['section_name']) : null;
        $position = isset($in['position']) ? (int)$in['position'] : null;

        $errors = [];

        if ($section_name === null) {
            $errors['section_name'] = 'Section_name is required';
        }
        if ($section_name !== null) {
            if ($section_name === '') {
                $errors['section_name'] = 'Section_name cannot be empty';
            } elseif (strlen($section_name) < 2) {
                $errors['section_name'] = 'Section_name must be at least 2 characters';
            }
        }
        if ($position !== null && $position <= 0)
            $errors['position'] = 'position required if provided';
        return [
            $errors === [],
            $errors,
            array_filter([
                'section_name' => $section_name,
                'position' => $position
            ], fn($v) => $v !== null)
        ];
    }
}
