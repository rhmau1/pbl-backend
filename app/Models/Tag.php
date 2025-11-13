<?php

namespace App\Models;

final class Tag
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $slug
    ) {}
}
