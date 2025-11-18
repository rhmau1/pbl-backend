<?php

namespace App\Models;

final class Sejarah
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $content,
        public string $updated_at
    ) {}
}
