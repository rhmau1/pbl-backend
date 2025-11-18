<?php

namespace App\Requests;

final class FooterItemRequest
{
    public static function validateCreate(array $in): array
    {
        $label = isset($in['label']) ? trim((string) $in['label']) : null;
        $content = isset($in['content']) ? trim((string) $in['content']) : null;
        $url = isset($in['url']) ? trim((string) $in['url']) : null;
        $section_id = isset($in['section_id']) ? (int)$in['section_id'] : null;
        $position = isset($in['position']) ? (int)$in['position'] : null;

        $errors = [];

        if ($label === null) {
            $errors['label'] = 'Label is required';
        }
        if ($label !== null) {
            if ($label === '') {
                $errors['label'] = 'Label cannot be empty';
            } elseif (strlen($label) < 2) {
                $errors['label'] = 'Label must be at least 2 characters';
            }
        }
        if ($content === '') $errors['content'] = 'content required';
        if ($position === null) $errors['position'] = 'position required';
        if ($section_id === null) $errors['section_id'] = 'section_id required';

        return [
            $errors === [],
            $errors,
            array_filter([
                'label' => $label,
                'content' => $content,
                'section_id' => $section_id,
                'position' => $position,
                'url' => $url
            ], fn($v) => $v !== null)
        ];
    }
    public static function validateUpdate(array $in): array
    {
        $label = isset($in['label']) ? trim((string) $in['label']) : null;
        $content = isset($in['content']) ? trim((string) $in['content']) : null;
        $url = isset($in['url']) ? trim((string) $in['url']) : null;
        $section_id = isset($in['section_id']) ? (int)$in['section_id'] : null;
        $position = isset($in['position']) ? (int)$in['position'] : null;

        $errors = [];

        if (isset($in['label']) && trim((string)$in['label']) === '') {
            $errors['label'] = 'label required if provided';
        }
        if (isset($in['content']) && trim((string)$in['content']) === '') {
            $errors['content'] = 'content required if provided';
        }
        if (isset($in['url']) && trim((string)$in['url']) === '') {
            $errors['url'] = 'url required if provided';
        }
        if ($position !== null && $position <= 0)
            $errors['position'] = 'position required if provided';
        if ($section_id !== null && $section_id <= 0)
            $errors['section_id'] = 'section_id required if provided';
        return [
            $errors === [],
            $errors,
            array_filter([
                'label' => $label,
                'content' => $content,
                'section_id' => $section_id,
                'position' => $position,
                'url' => $url
            ], fn($v) => $v !== null)
        ];
    }
}
