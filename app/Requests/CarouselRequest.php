<?php

namespace App\Requests;

final class CarouselRequest
{
    public static function validate(array $in): array
    {
        $carousel_url = isset($in['carousel_url']) ? trim((string) $in['carousel_url']) : null;
        $position = isset($in['position']) ? (int) $in['position'] : null;

        $errors = [];

        if ($carousel_url !== null && $carousel_url === '')
            $errors['carousel_url'] = 'Carousel URL required if provided';

        if ($position !== null && $position < 0)
            $errors['position'] = 'Position must be 0 or greater';

        return [
            $errors === [],
            $errors,
            array_filter(['carousel_url' => $carousel_url, 'position' => $position], fn($v) => $v !== null)
        ];
    }
}
