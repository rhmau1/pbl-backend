<?php

namespace App\Models;

final class NavbarLogo
{
    public function __construct(
        public ?int $id,
        public string $logo_url,
        public string $url,
        public int $is_active
    ) {}
}
