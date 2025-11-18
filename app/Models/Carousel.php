<?php

namespace App\Models;

final class Carousel
{
    public function __construct(
        public ?int $id,
        public string $carousel_url,
        public int $position
    ) {}
}
