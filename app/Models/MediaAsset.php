<?php

namespace App\Models;

final class MediaAsset
{
    public function __construct(
        public ?int $id,
        public string $type,
        public string $url,
        public ?string $altText,
        public ?string $caption,
        public ?int $bytes,
        public ?string $checksum,
        public int $ownerId,
        public int $visibility,
        public string $createdAt
    ) {}
}
