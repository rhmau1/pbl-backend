<?php

namespace App\Models;

final class FooterSection
{
    public function __construct(
        public ?int $id,
        public string $section_name,
        public int $position
    ) {}
}
