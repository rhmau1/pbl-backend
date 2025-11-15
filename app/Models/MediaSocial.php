<?php

namespace App\Models;

final class MediaSocial
{
    public function __construct(
        public ?int $id,
        public string $platform,
        public string $url,
        public string $icon,
        public int $position
    ) {}
}
