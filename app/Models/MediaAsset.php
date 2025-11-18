<?php

namespace App\Models;

final class MediaAsset
{
    public function __construct(
        public ?int $id,
        public string $type,
        public string $url,
        public ?string $alt_text,
        public ?string $caption,
        public ?int $bytes,
        public ?string $checksum,
        public int $owner_id,
        public int $visibility,
        public string $created_at
    ) {}
}
