<?php

namespace App\Models;

final class FooterItem
{
    public function __construct(
        public ?int $id,
        public int $section_id,
        public ?string $label,
        public ?string $content,
        public ?string $url,
        public int $position
    ) {}
}
