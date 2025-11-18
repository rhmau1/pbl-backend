<?php

namespace App\Requests;

final class NavbarLogoRequest
{
    public static function validate(array $in): array
    {
        $logo_url = isset($in['logo_url']) ? trim((string) $in['logo_url']) : null;
        $url = isset($in['url']) ? trim((string) $in['url']) : null;
        $is_active = isset($in['is_active']) ? (int) $in['is_active'] : null;

        $errors = [];

        if ($logo_url !== null && $logo_url === '')
            $errors['logo_url'] = 'Logo URL required if provided';

        if ($url !== null && $url === '')
            $errors['url'] = 'URL required if provided';

        if ($is_active !== null && !in_array($is_active, [0, 1]))
            $errors['is_active'] = 'Is active must be 0 or 1';

        return [
            $errors === [],
            $errors,
            array_filter(['logo_url' => $logo_url, 'url' => $url, 'is_active' => $is_active], fn($v) => $v !== null)
        ];
    }
}
