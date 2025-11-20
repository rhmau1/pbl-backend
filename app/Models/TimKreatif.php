<?php

namespace App\Models;

final class TimKreatif
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $role,
        public ?string $photo_url,
        public ?array $skills,
        public int $position
    ) {}
}
