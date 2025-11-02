<?php

namespace App\Models;

final class Role
{
    public function __construct(
        public ?int $id,
        public string $name
    ) {}
}
