<?php

namespace App\Models;

final class Category
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $slug,
        public string $type
    ) {}
}
