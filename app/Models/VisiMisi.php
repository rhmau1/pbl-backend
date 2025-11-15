<?php

namespace App\Models;

final class VisiMisi
{
    public function __construct(
        public ?int $id,
        public string $type,
        public string $content,
        public int $position,
        public string $updatedAt
    ) {}
}
