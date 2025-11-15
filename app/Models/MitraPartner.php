<?php

namespace App\Models;

final class MitraPartner
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $logo_url,
        public string $website_url,
        public int $position
    ) {}
}
